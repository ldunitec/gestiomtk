<?php

namespace Modules\Mikrotik\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Mikrotik\Entities\{Router, Lote, Ficha};
use Modules\Mikrotik\Services\{RouterFactory, HotspotService, FichasService, ErrorRouter, ScriptsHotspot};

class MikrotikController extends Controller
{
    public function __construct(private RouterFactory $factory, private HotspotService $hotspot, private FichasService $service) {}

    public function index()
    {
        return view('mikrotik::index', ['routers' => Router::orderBy('nombre')->get(),
            'activas' => Ficha::where('estado', 'activa')->whereNull('revocada_at')->where(fn ($v) => $v->whereNull('vence_at')->orWhere('vence_at', '>', CarbonImmutable::now('UTC')))->count(),
            'pendientes' => Ficha::whereIn('estado', ['pendiente','error','revocacion_pendiente'])->count(),
            'eventos' => DB::table('mt_eventos')->join('mt_routers', 'mt_routers.id', '=', 'mt_eventos.router_id')
                ->select('mt_eventos.*', 'mt_routers.nombre')->orderByDesc('mt_eventos.id')->limit(20)->get()]);
    }

    public function configuracion()
    {
        return view('mikrotik::configuracion', ['routers' => Router::orderBy('nombre')->get()]);
    }

    public function guardar(Request $r, ?Router $router = null)
    {
        $d = $r->validate([
            'nombre' => 'required|string|max:100', 'host' => ['bail','required','string','max:253', function ($attribute, $value, $fail) {
                if (!filter_var($value, FILTER_VALIDATE_IP) && !preg_match('/^(?=.{1,253}$)[a-z0-9](?:[a-z0-9.-]*[a-z0-9])?$/iD', $value)) {
                    $fail('Use una IP o nombre DNS, sin protocolo, ruta ni puerto.');
                }
            }],
            'puerto' => 'required|integer|min:1|max:65535', 'tls' => 'required|boolean',
            'usuario_api' => 'required|string|max:100', 'password' => ($router ? 'nullable' : 'required').'|string|max:255',
            'servidor_hotspot' => 'required|string|max:100', 'ssid' => 'required|string|max:100',
            'portal_url' => ['bail','required','url:http,https','max:255', function ($attribute, $value, $fail) {
                $p = parse_url($value);
                if (!$p || ($p['path'] ?? '') !== '/login' || isset($p['user']) || isset($p['pass']) || isset($p['query']) || isset($p['fragment'])) {
                    $fail('La URL del portal debe terminar en /login, sin credenciales, parámetros ni fragmentos.');
                }
            }],
            'velocidad' => ['required','regex:/^[1-9]\d{0,4}[kKM]?\/[1-9]\d{0,4}[kKM]?$/D'],
        ]);
        $router = DB::transaction(function () use ($router, $d, $r) {
            $router = $router ? Router::whereKey($router->id)->lockForUpdate()->firstOrFail() : new Router(['uuid' => (string) Str::uuid()]);
            if (isset($d['password']) && $d['password'] !== '') { $d['clave_api'] = $d['password']; }
            unset($d['password']);
            // Evitar redirigir fichas emitidas a otro equipo/Hotspot por una edición accidental.
            if ($router->exists && $router->fichas()->withTrashed()->exists()) {
                foreach (['host','puerto','tls','servidor_hotspot','portal_url','ssid'] as $campo) {
                    $anterior = $campo === 'tls' ? (int) $router->$campo : $router->$campo;
                    $nuevo = $campo === 'tls' ? (int) $d[$campo] : $d[$campo];
                    if ((string) $anterior !== (string) $nuevo) {
                        throw \Illuminate\Validation\ValidationException::withMessages([$campo => 'Este router tiene historial de fichas. Registre otro router para cambiar destino, transporte, Hotspot o portal.']);
                    }
                }
            }
            $router->fill($d);
            if ($router->isDirty(['host','puerto','tls','usuario_api','clave_api','servidor_hotspot','velocidad'])) {
                $router->preparado_at = null;
            }
            $router->save();
            $this->service->evento($router, 'guardar_configuracion', true, $r->user()->id);
            return $router;
        });
        return redirect()->route('mt.configuracion')->with('ok', 'Router guardado. Pruebe la conexión y prepare el Hotspot antes de emitir fichas.');
    }

    public function probar(Request $request, Router $router)
    {
        try {
            $d = $this->hotspot->datos($this->factory->abrir($router), $router);
            $router->update(['verificado_at' => CarbonImmutable::now('UTC'), 'version' => $d['resource']['version'] ?? null]);
            $this->service->evento($router, 'probar_conexion', true, $request->user()->id);
            return back()->with('ok', 'Conexión correcta: '.$d['identity'].' · RouterOS '.($d['resource']['version'] ?? '?').'. La prueba no cambia la configuración del equipo.');
        } catch (ErrorRouter $e) {
            $this->service->evento($router, 'probar_conexion', false, $request->user()->id);
            return back()->withErrors(['router' => $e->getMessage()]);
        }
    }

    public function preparacion(Router $router)
    {
        return view('mikrotik::preparacion', ['router' => $router, 'barrido' => ScriptsHotspot::barrido($router), 'login' => ScriptsHotspot::login($router)]);
    }

    public function preparar(Request $r, Router $router)
    {
        $r->validate(['confirmar' => 'accepted']);
        try {
            DB::transaction(function () use ($router, $r) {
                $router = Router::whereKey($router->id)->lockForUpdate()->firstOrFail();
                $this->hotspot->preparar($this->factory->abrir($router), $router);
                $this->service->evento($router, 'preparar_hotspot', true, $r->user()->id);
            });
            return redirect()->route('mt.configuracion')->with('ok', 'Hotspot preparado: perfiles y control de vencimiento verificados.');
        } catch (ErrorRouter $e) {
            // Los cambios remotos no se revierten junto a la transacción SQL.
            $router->update(['preparado_at' => null]);
            $this->service->evento($router, 'preparar_hotspot', false, $r->user()->id);
            return back()->withErrors(['router' => $e->getMessage()]);
        }
    }

    public function estado(Router $router)
    {
        try {
            $datos = $this->hotspot->datos($this->factory->abrir($router), $router);
            return view('mikrotik::estado', compact('router','datos'));
        } catch (ErrorRouter $e) { return back()->withErrors(['router' => $e->getMessage()]); }
    }

    public function empleados(Request $r)
    {
        return $this->fichas($r, true);
    }

    public function fichas(Request $r, bool $empleados = false)
    {
        $d = $r->validate(['router_id' => 'nullable|integer|exists:mt_routers,id', 'q' => 'nullable|string|max:100',
            'impresion' => ['nullable', Rule::in(['pendientes','impresas'])],
            'estado' => ['nullable', Rule::in(['activa','error','pendiente','revocacion_pendiente','revocada','vencida'])]]);
        $q = Ficha::with(['router','lote']);
        $empleados ? $q->where('inicio_vigencia', 'permanente') : $q->where('inicio_vigencia', '!=', 'permanente');
        if (!empty($d['router_id'])) { $q->where('router_id', $d['router_id']); }
        if (!empty($d['q'])) { $q->where(function ($busqueda) use ($d) {
            $busqueda->where('usuario', 'like', '%'.$d['q'].'%')->orWhere('nombre', 'like', '%'.$d['q'].'%')
                ->orWhere(function ($heredada) use ($d) { $heredada->whereNull('nombre')->whereHas('lote', fn ($l) => $l->where('visitante', 'like', '%'.$d['q'].'%')); });
        }); }
        if (($d['impresion'] ?? '') === 'pendientes') { $q->disponiblesParaImprimir()->whereNull('impresa_at'); }
        if (($d['impresion'] ?? '') === 'impresas') { $q->whereNotNull('impresa_at'); }
        if (!empty($d['estado'])) {
            if ($d['estado'] === 'vencida') { $q->whereNull('revocada_at')->where('vence_at', '<=', CarbonImmutable::now('UTC')); }
            else {
                $q->where('estado', $d['estado']);
                if ($d['estado'] === 'activa') { $q->whereNull('revocada_at')->where(fn ($v) => $v->whereNull('vence_at')->orWhere('vence_at', '>', CarbonImmutable::now('UTC'))); }
            }
        }
        return view('mikrotik::fichas', ['fichas' => $q->latest('id')->paginate(30)->withQueryString(),
            'empleados' => $empleados, 'routers' => Router::orderBy('nombre')->get(), 'solicitud' => (string) Str::uuid()]);
    }

    public function crearEmpleado(Request $r)
    {
        $d = $r->validate(['router_id' => 'required|integer|exists:mt_routers,id',
            'visitante' => 'required|string|max:100', 'solicitud_uuid' => 'required|uuid']);
        $d['dias'] = 0;
        $d['cantidad'] = 1;
        $lote = $this->service->crear(Router::findOrFail($d['router_id']), $d, $r->user()->id, true);
        $result = $this->service->publicar($lote, $r->user()->id);
        return redirect()->route('mt.lote', $lote)->with('ok', $result['correctas'].' fichas de empleado confirmadas; '.$result['errores'].' pendientes por error.');
    }

    public function crear(Request $r)
    {
        $d = $r->validate(['router_id' => 'required|integer|exists:mt_routers,id', 'dias' => 'required|integer|in:1,2,7',
            'cantidad' => 'required|integer|min:1|max:20', 'visitante' => 'nullable|string|max:100', 'solicitud_uuid' => 'required|uuid']);
        $lote = $this->service->crear(Router::findOrFail($d['router_id']), $d, $r->user()->id);
        $result = $this->service->publicar($lote, $r->user()->id);
        return redirect()->route('mt.lote', $lote)->with('ok', $result['correctas'].' fichas confirmadas; '.$result['errores'].' pendientes por error.');
    }

    public function lote(Lote $lote)
    {
        $lote->load(['router','fichas.router']);
        return response()->view('mikrotik::lote', compact('lote'))->header('Cache-Control', 'no-store, private');
    }

    public function publicar(Request $r, Lote $lote)
    {
        $result = $this->service->publicar($lote, $r->user()->id, true);
        return back()->with('ok', $result['correctas'].' fichas confirmadas; '.$result['errores'].' pendientes por error. La vigencia original se conserva.');
    }

    public function imprimir(Request $r, Lote $lote)
    {
        $d = $r->validate([
            'ficha_id' => 'nullable|integer|min:1|prohibits:fichas,seleccion',
            'seleccion' => 'sometimes|in:1',
            'fichas' => 'required_if:seleccion,1|nullable|array|min:1|max:20',
            'fichas.*' => 'required|integer|min:1|distinct',
        ]);
        $fichas = $lote->fichas()->with(['router','lote'])->get();
        if ($id = $r->input('ficha_id')) {
            $fichas = $fichas->where('id', (int) $id);
            abort_if($fichas->isEmpty(), 404);
            abort_unless($fichas->first()->imprimible(), 422, 'La ficha no está confirmada, fue revocada o ya venció.');
        }
        if (isset($d['fichas'])) {
            $ids = array_map('intval', $d['fichas']);
            $seleccionadas = $fichas->whereIn('id', $ids);
            // Validar todas las fichas dentro del lote: no ignorar IDs ajenos.
            abort_if($seleccionadas->count() !== count($ids), 404);
            $fichas = $seleccionadas;
        }
        $omitidas = $fichas->filter(fn ($f) => !$f->imprimible())->count();
        $fichas = $fichas->filter(fn ($f) => $f->imprimible());
        abort_if($fichas->isEmpty(), 422, 'El lote no tiene fichas activas, confirmadas y vigentes para imprimir.');
        return response()->view('mikrotik::imprimir', compact('lote','fichas','omitidas'))->header('Cache-Control', 'no-store, private')->header('Referrer-Policy', 'no-referrer');
    }

    private function alcanceFichas(array $d)
    {
        $q = Ficha::query();
        ($d['empleados'] ?? false) ? $q->where('inicio_vigencia', 'permanente') : $q->where('inicio_vigencia', '!=', 'permanente');
        if (!empty($d['router_id'])) { $q->where('router_id', $d['router_id']); }
        return $q;
    }

    public function imprimirListado(Request $r)
    {
        $d = $r->validate(['empleados'=>'required|boolean', 'router_id'=>'nullable|integer|exists:mt_routers,id',
            'modo'=>'required|in:todas,pendientes,seleccion',
            'fichas'=>'required_if:modo,seleccion|nullable|array|min:1|max:500', 'fichas.*'=>'required|integer|min:1|distinct']);
        $q = $this->alcanceFichas($d)->with(['router','lote']);
        if ($d['modo'] === 'seleccion') {
            $ids = array_map('intval', $d['fichas']);
            $fichas = $q->whereIn('id', $ids)->orderBy('id')->get();
            abort_if($fichas->count() !== count($ids), 404);
            // Las seleccionadas del panel deben seguir activas y sin impresión confirmada.
            $validas = $fichas->filter(fn ($f) => $f->imprimible() && !$f->impresa_at);
            $omitidas = $fichas->count() - $validas->count();
            $fichas = $validas;
        } else {
            $q->disponiblesParaImprimir();
            if ($d['modo'] === 'pendientes') { $q->whereNull('impresa_at'); }
            $fichas = $q->orderBy('lote_id')->orderBy('id')->get();
            $omitidas = 0;
        }
        abort_if($fichas->isEmpty(), 422, 'No hay fichas disponibles para esta impresión.');
        $lote = null;
        return response()->view('mikrotik::imprimir', compact('lote','fichas','omitidas'))
            ->header('Cache-Control', 'no-store, private')->header('Referrer-Policy', 'no-referrer');
    }

    public function confirmarImpresion(Request $r)
    {
        $d = $r->validate(['seleccion_impresa'=>'required|string|json']);
        $lista = json_decode($d['seleccion_impresa'], true);
        $d = \Illuminate\Support\Facades\Validator::make(['fichas'=>$lista], ['fichas'=>'required|array|min:1', 'fichas.*'=>'required|integer|min:1|distinct'])->validate();
        $ids = array_map('intval', $d['fichas']);
        $resultado = DB::transaction(function () use ($ids, $r) {
            $fichas = Ficha::whereIn('id', $ids)->lockForUpdate()->get();
            abort_if($fichas->count() !== count($ids), 404);
            $n = 0;
            foreach ($fichas as $f) {
                if ($f->impresa_at || !$f->imprimible()) { continue; }
                $f->update(['impresa_at'=>CarbonImmutable::now('UTC'),'impresa_por'=>$r->user()->id]);
                $this->service->evento($f->router, 'confirmar_impresion', true, $r->user()->id, $f->id);
                $n++;
            }
            return ['n'=>$n, 'empleados'=>$fichas->first()->inicio_vigencia === 'permanente'];
        });
        return redirect()->route($resultado['empleados'] ? 'mt.empleados' : 'mt.fichas')->with('ok', $resultado['n'].' fichas registradas como impresas. Las fechas de impresión anteriores se conservan.');
    }

    public function editarFicha(Ficha $ficha)
    {
        $ficha->load(['router','lote']);
        return view('mikrotik::editar-ficha', compact('ficha'));
    }

    public function actualizarFicha(Request $r, Ficha $ficha)
    {
        $d = $r->validate(['nombre'=>($ficha->inicio_vigencia === 'permanente' ? 'required' : 'nullable').'|string|max:100']);
        DB::transaction(function () use ($ficha, $d, $r) {
            $ficha = Ficha::whereKey($ficha->id)->lockForUpdate()->firstOrFail();
            $ficha->update(['nombre'=>$d['nombre'] ?? '']);
            $this->service->evento($ficha->router, 'editar_ficha', true, $r->user()->id, $ficha->id);
        });
        return redirect()->route($ficha->inicio_vigencia === 'permanente' ? 'mt.empleados' : 'mt.fichas')->with('ok', 'Nombre de la ficha actualizado.');
    }

    public function eliminarFichas(Request $r)
    {
        $d = $r->validate(['empleados'=>'required|boolean','router_id'=>'nullable|integer|exists:mt_routers,id',
            'confirmar'=>'accepted','modo'=>'sometimes|in:seleccion,todos', 'estados'=>'sometimes|in:error,revocada,ambos',
            'fichas'=>'required_if:modo,seleccion|nullable|array|min:1|max:500', 'fichas.*'=>'required|integer|min:1|distinct']);
        $q = $this->alcanceFichas($d);
        if (($d['modo'] ?? 'todos') === 'seleccion') {
            $seleccion = array_map('intval', $d['fichas']);
            $fichas = $q->whereIn('id', $seleccion)->get(['id','estado']);
            abort_if($fichas->count() !== count($seleccion), 404);
            abort_if($fichas->contains(fn ($f) => !in_array($f->estado, ['error','revocada'], true)), 422,
                'Solo se pueden eliminar fichas con error o revocación confirmada.');
            $ids = $fichas->pluck('id');
        } else {
            $estados = match ($d['estados'] ?? 'error') { 'revocada'=>['revocada'], 'ambos'=>['error','revocada'], default=>['error'] };
            $ids = $q->whereIn('estado', $estados)->pluck('id');
        }
        $eliminadas = 0; $pendientes = 0;
        foreach ($ids as $id) {
            $resultado = $this->service->eliminarFicha((int) $id, $r->user()->id);
            if ($resultado === true) { $eliminadas++; }
            elseif ($resultado === false) { $pendientes++; }
        }
        return back()->with($pendientes ? 'aviso' : 'ok', $eliminadas.' fichas eliminadas. '.$pendientes.' conservadas porque no se confirmó su bloqueo en el router.');
    }

    public function revocar(Request $r, Ficha $ficha)
    {
        $this->service->revocar($ficha, $r->user()->id);
        $pendiente = $ficha->fresh()->estado === 'revocacion_pendiente';
        return back()->with($pendiente ? 'aviso' : 'ok', $pendiente ? 'Revocación pendiente: todavía no se confirmó el bloqueo en el router. Se reintentará al conciliar.' : 'Ficha revocada y sesiones desconectadas.');
    }

    public function conciliar(Request $r, Router $router)
    {
        $n = $this->service->conciliar($router->id, $r->user()->id);
        return back()->with($n ? 'aviso' : 'ok', $n ? $n.' fichas siguen pendientes de sincronización o bloqueo.' : 'Primeros ingresos, vencimientos y revocaciones sincronizados.');
    }

    public function elegirSesiones()
    {
        return view('mikrotik::elegir-sesiones', ['routers' => Router::orderBy('nombre')->get()]);
    }

    public function sesiones(Router $router)
    {
        try {
            $sesiones = $this->hotspot->sesiones($this->factory->abrir($router), $router);
            $fichas = $router->fichas()->whereIn('usuario', array_column($sesiones, 'user'))->get()->keyBy('usuario');
            return view('mikrotik::sesiones', compact('router','sesiones','fichas'));
        } catch (ErrorRouter $e) { return back()->withErrors(['router' => $e->getMessage()]); }
    }

    public function desconectar(Request $r, Ficha $ficha)
    {
        try {
            $this->hotspot->desconectar($this->factory->abrir($ficha->router), $ficha);
            $this->service->evento($ficha->router, 'desconectar', true, $r->user()->id, $ficha->id);
            return back()->with('ok', 'Sesión desconectada. La ficha conserva su vigencia y puede volver a ingresar.');
        } catch (ErrorRouter $e) {
            $this->service->evento($ficha->router, 'desconectar', false, $r->user()->id, $ficha->id);
            return back()->withErrors(['router' => $e->getMessage()]);
        }
    }
}




