<?php

namespace Modules\Gym\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Gym\Entities\{Socio, Plan, Membresia, Acceso, Asistencia};
use Modules\Gym\Services\{MembresiaService, AccesoService};

class GymController extends Controller
{
    public function index()
    {
        $hoy = MembresiaService::hoy();
        $limite = CarbonImmutable::parse($hoy)->addDays(7)->toDateString();
        $vigentes = Membresia::where('anulada', false)->where('inicio', '<=', $hoy)->where('fin', '>=', $hoy)
            ->whereColumn('pagado_centavos', '>=', 'precio_centavos');
        $desde = CarbonImmutable::today(config('gym.timezone'))->utc();
        $hasta = $desde->addDay();
        return view('gym::index', [
            'socios' => Socio::where('estado', 'activo')->count(),
            'vigentes' => (clone $vigentes)->count(),
            'vencen' => (clone $vigentes)->where('fin', '<=', $limite)->count(),
            'visitas' => Asistencia::where('entrada', '>=', $desde)->where('entrada', '<', $hasta)->count(),
            'accesos' => Acceso::with('socio')->latest('id')->limit(15)->get(),
        ]);
    }

    public function socios(Request $r)
    {
        $d = $r->validate(['q' => 'nullable|string|max:100']);
        $q = Socio::query();
        if ($buscar = trim($d['q'] ?? '')) {
            $q->where(function ($q) use ($buscar) {
                foreach (['codigo', 'nombres', 'apellidos', 'documento'] as $col) {
                    $q->orWhere($col, 'like', '%'.$buscar.'%');
                }
            });
        }
        return view('gym::socios', ['socios' => $q->orderBy('apellidos')->paginate(30)->withQueryString()]);
    }

    public function guardarSocio(Request $r, ?Socio $socio = null)
    {
        $socio = $socio ?? new Socio();
        $d = $r->validate(['nombres' => 'required|string|max:100', 'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:40', 'documento' => 'nullable|string|max:50', 'email' => 'nullable|email|max:255',
            'estado' => 'required|in:activo,suspendido,inactivo']);
        // El código no cambia con el nombre, para conservar tarjetas y referencias.
        if (!$socio->exists) {
            $d['codigo'] = 'GYM-'.Str::upper(Str::random(12));
        }
        DB::transaction(function () use ($socio, $d) {
            if ($socio->exists) {
                Socio::whereKey($socio->id)->lockForUpdate()->firstOrFail();
            }
            $socio->fill($d)->save();
        });
        return redirect()->route('gym.socios.ver', $socio)->with('ok', 'Socio guardado.');
    }

    public function socio(Socio $socio)
    {
        $ultima = $socio->membresias()->where('anulada', false)->max('fin');
        $siguiente = $ultima && $ultima >= MembresiaService::hoy()
            ? CarbonImmutable::parse($ultima)->addDay()->toDateString() : MembresiaService::hoy();
        return view('gym::socio', ['socio' => $socio, 'planes' => Plan::where('activo', true)->get(),
            'membresias' => $socio->membresias()->with('pagos')->latest('id')->paginate(10), 'siguiente' => $siguiente]);
    }

    public function planes()
    {
        return view('gym::planes', ['planes' => Plan::orderBy('nombre')->paginate(30)]);
    }

    public function guardarPlan(Request $r, ?Plan $plan = null)
    {
        $d = $r->validate(['nombre' => 'required|string|max:100', 'dias' => 'required|integer|min:1|max:3650',
            'precio' => ['required', 'regex:/^\d{1,7}(\.\d{1,2})?$/D'],
            'limite_visitas' => 'nullable|integer|min:1|max:100000', 'activo' => 'required|boolean']);
        $d['precio_centavos'] = MembresiaService::centavos($d['precio']);
        unset($d['precio']);
        ($plan ?? new Plan())->fill($d)->save();
        return redirect()->route('gym.planes')->with('ok', 'Plan guardado. Las membresías existentes conservan sus condiciones.');
    }

    public function membresia(Request $r, Socio $socio, MembresiaService $service)
    {
        $d = $r->validate(['plan_id' => 'required|integer|exists:gym_planes,id', 'inicio' => 'required|date_format:Y-m-d|after_or_equal:'.MembresiaService::hoy(),
            'abono' => ['required', 'regex:/^\d{1,7}(\.\d{1,2})?$/D']]);
        $service->crear($socio, $d['plan_id'], $d['inicio'], $d['abono'], $r->user()->id);
        return back()->with('ok', 'Membresía creada. El acceso requiere pago completo y vigencia.');
    }

    public function pago(Request $r, Membresia $membresia, MembresiaService $service)
    {
        $d = $r->validate(['importe' => ['required', 'regex:/^\d{1,7}(\.\d{1,2})?$/D'], 'referencia' => 'nullable|string|max:150']);
        $service->pagar($membresia, $d['importe'], $d['referencia'] ?? null, $r->user()->id);
        return back()->with('ok', 'Pago registrado.');
    }

    public function anular(Membresia $membresia)
    {
        DB::transaction(function () use ($membresia) {
            Socio::whereKey($membresia->socio_id)->lockForUpdate()->firstOrFail();
            $membresia->update(['anulada' => true]);
        });
        return back()->with('ok', 'Membresía anulada. Los pagos permanecen en el historial; esto no registra un reembolso.');
    }

    public function accesos()
    {
        return view('gym::accesos', ['solicitud' => (string) Str::uuid(), 'accesos' => Acceso::with('socio')->latest('id')->paginate(25)]);
    }

    private function datosAcceso(Request $r): array
    {
        if (is_string($r->input('codigo'))) {
            $r->merge(['codigo' => Str::upper(trim($r->input('codigo')))]);
        }
        return $r->validate(['codigo' => ['required', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/D'],
            'movimiento' => 'required|in:entrada,salida', 'solicitud_id' => 'required|uuid']);
    }

    public function registrar(Request $r, AccesoService $service)
    {
        $d = $this->datosAcceso($r);
        $a = $service->registrar($d['codigo'], $d['movimiento'], $d['solicitud_id'], $r->user()->id);
        return redirect()->route('gym.accesos')->with('resultado', ['permitido' => $a->permitido, 'motivo' => $a->motivo, 'codigo' => $a->codigo]);
    }

    private function autorizarToken(Request $r): void
    {
        abort_unless($r->user()->tokenCan('gym:acceso'), 403, 'El token requiere la capacidad gym:acceso.');
    }

    public function validarApi(Request $r, AccesoService $service)
    {
        $this->autorizarToken($r);
        $d = $r->validate(['codigo' => 'required|string|max:40']);
        return response()->json($service->evaluar(Socio::where('codigo', Str::upper(trim($d['codigo'])))->first()));
    }

    public function registrarApi(Request $r, AccesoService $service)
    {
        $this->autorizarToken($r);
        $d = $this->datosAcceso($r);
        $a = $service->registrar($d['codigo'], $d['movimiento'], $d['solicitud_id'], $r->user()->id);
        return response()->json($a->only(['id', 'solicitud_id', 'codigo', 'movimiento', 'permitido', 'motivo', 'momento']));
    }

    public function reportes(Request $r)
    {
        $r->merge(['desde' => $r->input('desde', CarbonImmutable::today(config('gym.timezone'))->subDays(6)->toDateString()),
            'hasta' => $r->input('hasta', MembresiaService::hoy())]);
        $d = $r->validate(['desde' => 'required|date_format:Y-m-d', 'hasta' => 'required|date_format:Y-m-d|after_or_equal:desde',
            'codigo' => 'nullable|string|max:40', 'csv' => 'nullable|in:1']);
        $desde = CarbonImmutable::parse($d['desde'], config('gym.timezone'))->startOfDay();
        $hasta = CarbonImmutable::parse($d['hasta'], config('gym.timezone'))->addDay()->startOfDay();
        if ($desde->diffInDays($hasta) > 366) {
            throw \Illuminate\Validation\ValidationException::withMessages(['hasta' => 'El reporte admite un máximo de 366 días.']);
        }
        $asistencias = Asistencia::with('socio')->where('entrada', '>=', $desde->utc())->where('entrada', '<', $hasta->utc());
        $denegados = Acceso::with('socio')->where('permitido', false)->where('momento', '>=', $desde->utc())->where('momento', '<', $hasta->utc());
        if (!empty($d['codigo'])) {
            $codigo = Str::upper(trim($d['codigo']));
            $asistencias->whereHas('socio', fn ($q) => $q->where('codigo', $codigo));
            $denegados->where('codigo', $codigo);
        }
        if ($r->boolean('csv')) {
            return response()->streamDownload(function () use ($asistencias) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF");
                fputcsv($out, ['Código', 'Nombre', 'Entrada (Honduras)', 'Salida (Honduras)', 'Estado']);
                foreach ($asistencias->orderBy('id')->lazyById(500) as $a) {
                    $fila = [$a->socio->codigo, $a->socio->nombre,
                        $a->entrada->timezone(config('gym.timezone'))->format('Y-m-d H:i:s'),
                        $a->salida?->timezone(config('gym.timezone'))->format('Y-m-d H:i:s'), $a->estado];
                    fputcsv($out, array_map(fn ($v) => preg_match('/^[\s]*[=+@-]/u', (string) $v) ? "'".$v : $v, $fila));
                }
                fclose($out);
            }, 'asistencia-gym.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }
        $total = (clone $asistencias)->count();
        $rechazos = (clone $denegados)->count();
        return view('gym::reportes', ['asistencias' => $asistencias->latest('entrada')->paginate(50, ['*'], 'asistencia_page')->withQueryString(),
            'denegados' => $denegados->latest('id')->paginate(50, ['*'], 'rechazo_page')->withQueryString(),
            'total' => $total, 'rechazos' => $rechazos]);
    }
}
