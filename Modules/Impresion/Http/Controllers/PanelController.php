<?php

namespace Modules\Impresion\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Impresion\Entities\Crey;
use Modules\Impresion\Services\FotoService;
use Modules\Impresion\Services\ZipFotosService;

class PanelController extends Controller
{
    protected function filters(Request $request): array
    {
        return $request->validate([
            'curso' => ['nullable', 'string', 'max:255'],
            'seccion' => ['nullable', 'string', 'max:255'],
            'modalidad' => ['nullable', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', Rule::in(['todos', 'pendientes', 'impresos'])],
        ]);
    }

    protected function query(array $filters, bool $estado = true)
    {
        $query = Crey::query();
        foreach (['curso', 'seccion', 'modalidad'] as $campo) {
            if (isset($filters[$campo]) && $filters[$campo] !== '') {
                $query->where($campo, $filters[$campo]);
            }
        }
        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                foreach (['nombres', 'apellidos', 'dni'] as $campo) {
                    $q->orWhere($campo, 'like', '%'.$filters['q'].'%');
                }
            });
        }
        if ($estado) {
            $status = $filters['estado'] ?? 'pendientes';
            if ($status === 'pendientes') {
                $query->whereNull('impreso_at');
            } elseif ($status === 'impresos') {
                $query->whereNotNull('impreso_at');
            }
        }

        return $query;
    }

    public function index(Request $request, FotoService $fotos)
    {
        $filters = $this->filters($request);
        $base = $this->query($filters, false);
        $total = (clone $base)->count();
        $impresos = (clone $base)->whereNotNull('impreso_at')->count();
        $alumnos = $this->query($filters)->orderBy('curso')->orderBy('seccion')
            ->orderBy('apellidos')->orderBy('nombres')->paginate(50)->withQueryString();
        $disponibles = $alumnos->getCollection()->mapWithKeys(fn ($a) => [$a->id => $fotos->resolve($a) !== null]);
        $cursos = Crey::whereNotNull('curso')->distinct()->orderBy('curso')->pluck('curso');
        $secciones = Crey::whereNotNull('seccion')->distinct()->orderBy('seccion')->pluck('seccion');
        $modalidades = Crey::whereNotNull('modalidad')->distinct()->orderBy('modalidad')->pluck('modalidad');
        $lotes = DB::table('impresion_lotes')->orderByDesc('created_at')->limit(10)->get();

        return view('impresion::panel.index', compact('filters', 'total', 'impresos', 'alumnos',
            'disponibles', 'cursos', 'secciones', 'modalidades', 'lotes'));
    }

    protected function selection(Request $request, bool $lock = false)
    {
        $filters = $this->filters($request);
        $data = $request->validate([
            'alcance' => ['required', Rule::in(['seleccion', 'seccion'])],
            'ids' => ['required_if:alcance,seleccion', 'array', 'max:500'],
            'ids.*' => ['integer', 'distinct'],
            'curso' => ['required_if:alcance,seccion', 'nullable', 'string', 'max:255'],
            'seccion' => ['required_if:alcance,seccion', 'nullable', 'string', 'max:255'],
        ]);
        $query = $this->query($filters);
        if ($data['alcance'] === 'seleccion') {
            $query->whereIn('id', $data['ids']);
        }
        $max = min(500, (int) config('impresion.max_lote', 500));
        $alumnos = ($lock ? $query->lockForUpdate() : $query)
            ->orderBy('apellidos')->orderBy('nombres')->limit($max + 1)->get();
        if ($alumnos->isEmpty() || $alumnos->count() > $max) {
            throw ValidationException::withMessages(['lote' => "Selecciona entre 1 y {$max} registros. Revisa los filtros y el estado."]);
        }
        if ($data['alcance'] === 'seleccion' && $alumnos->count() !== count($data['ids'])) {
            throw ValidationException::withMessages(['ids' => 'Algunos registros ya no coinciden con los filtros. Actualiza el panel.']);
        }

        return $alumnos;
    }

    public function foto(Crey $crey, FotoService $fotos)
    {
        $foto = $fotos->resolve($crey);
        abort_unless($foto, 404, 'Foto no disponible.');

        return response()->file($foto['path'], [
            'Content-Type' => $foto['mime'], 'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function upload(Request $request, Crey $crey, FotoService $fotos)
    {
        $request->validate(['foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240']]);
        $info = $fotos->imageInfo($request->file('foto')->getRealPath());
        if (!$info) {
            throw ValidationException::withMessages(['foto' => 'Selecciona una imagen JPG, PNG o WEBP válida.']);
        }
        $dir = storage_path('app/impresion/fotos');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $name = Str::uuid().'.'.$info['extension'];
        $request->file('foto')->move($dir, $name);
        try {
            DB::transaction(function () use ($crey, $name) {
                $row = Crey::whereKey($crey->id)->lockForUpdate()->firstOrFail();
                $row->foto_path = 'fotos/'.$name;
                $row->impreso_at = null;
                $row->impreso_por = null;
                $row->save();
            });
        } catch (\Throwable $e) {
            @unlink($dir.'/'.$name);
            throw $e;
        }

        return back()->with('success', 'Foto guardada. El alumno queda pendiente de impresión.');
    }

    public function zip(Request $request, ZipFotosService $zip)
    {
        $request->validate(['marcar_impresos' => ['nullable', 'boolean']]);
        $archive = null;
        try {
            DB::transaction(function () use ($request, $zip, &$archive) {
                $alumnos = $this->selection($request, true);
                $archive = $zip->build($alumnos);
                $marcar = $request->boolean('marcar_impresos');
                if ($marcar) {
                    Crey::whereIn('id', $archive['incluidos'])->update([
                        'impreso_at' => now(), 'impreso_por' => $request->user()->id,
                    ]);
                }
                $this->log($request, 'zip', $archive['incluidos'], count($archive['faltantes']), $marcar);
            });
        } catch (\Throwable $e) {
            if ($archive && is_file($archive['path'])) {
                unlink($archive['path']);
            }
            throw $e;
        }
        $nombre = Str::upper(Str::slug(($request->input('curso') ?: 'LOTE').'_SECCION_'
            .($request->input('seccion') ?: 'VARIAS'), '_'));

        return response()->download($archive['path'], 'FOTOS_'.$nombre.'_'.now()->format('Ymd_His').'.zip', [
            'X-Fotos-Incluidas' => count($archive['incluidos']),
            'X-Fotos-Faltantes' => count($archive['faltantes']),
            'Cache-Control' => 'private, no-store',
        ])->deleteFileAfterSend(true);
    }

    public function preparar(Request $request, FotoService $fotos)
    {
        $data = $request->validate([
            'ancho' => ['nullable', 'integer', 'min:20', 'max:100'],
            'alto' => ['nullable', 'integer', 'min:20', 'max:100'],
        ]);
        $alumnos = $this->selection($request);
        $snapshots = [];
        foreach ($alumnos as $alumno) {
            if ($foto = $fotos->resolve($alumno)) {
                $snapshots[$alumno->id] = $fotos->fingerprint($alumno, $foto);
            }
        }
        if (!$snapshots) {
            throw ValidationException::withMessages(['lote' => 'No hay fotos disponibles para imprimir.']);
        }
        // Conservar hasta diez vistas previas durante dos horas.
        $previas = collect($request->session()->get('impresion.previas', []))
            ->filter(fn ($v) => $v['expires'] > time())->take(-9)->all();
        $token = (string) Str::uuid();
        $previas[$token] = [
            'snapshots' => $snapshots, 'faltantes' => $alumnos->count() - count($snapshots),
            'ancho' => $data['ancho'] ?? 30, 'alto' => $data['alto'] ?? 40,
            'expires' => time() + 7200,
        ];
        $request->session()->put('impresion.previas', $previas);

        return redirect()->route('impresion.vista', $token);
    }

    protected function previa(Request $request, string $token): array
    {
        $previa = $request->session()->get('impresion.previas.'.$token);
        abort_unless($previa && $previa['expires'] > time(), 404, 'Vista previa vencida. Prepara nuevamente el lote.');

        return $previa;
    }

    public function vista(Request $request, string $token)
    {
        $previa = $this->previa($request, $token);
        $alumnos = Crey::whereIn('id', array_keys($previa['snapshots']))->orderBy('apellidos')->orderBy('nombres')->get();

        return response()->view('impresion::panel.imprimir', compact('previa', 'alumnos', 'token'))
            ->header('Cache-Control', 'private, no-store');
    }

    public function confirmar(Request $request, string $token, FotoService $fotos)
    {
        $previa = $this->previa($request, $token);
        DB::transaction(function () use ($request, $previa, $fotos) {
            $alumnos = Crey::whereIn('id', array_keys($previa['snapshots']))->lockForUpdate()->get();
            if ($alumnos->count() !== count($previa['snapshots'])) {
                throw ValidationException::withMessages(['lote' => 'El lote cambió. Prepara nuevamente la impresión.']);
            }
            foreach ($alumnos as $alumno) {
                $foto = $fotos->resolve($alumno);
                if (!$foto || $fotos->fingerprint($alumno, $foto) !== $previa['snapshots'][$alumno->id]) {
                    throw ValidationException::withMessages(['lote' => 'Una foto o un registro cambió. Prepara nuevamente la impresión.']);
                }
            }
            $ids = $alumnos->modelKeys();
            Crey::whereIn('id', $ids)->update(['impreso_at' => now(), 'impreso_por' => $request->user()->id]);
            $this->log($request, 'impresion_confirmada', $ids, $previa['faltantes'], true);
        });
        $request->session()->forget('impresion.previas.'.$token);

        return redirect()->route('impresion.panel')->with('success', 'Impresión confirmada. Solo se marcaron los alumnos incluidos.');
    }

    protected function log(Request $request, string $accion, array $ids, int $faltantes, bool $marcar): void
    {
        DB::table('impresion_lotes')->insert([
            'id' => (string) Str::uuid(), 'usuario_id' => $request->user()->id,
            'accion' => $accion, 'incluidos' => count($ids), 'faltantes' => $faltantes,
            'marcados' => $marcar ? count($ids) : 0, 'alumno_ids' => json_encode($ids),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
