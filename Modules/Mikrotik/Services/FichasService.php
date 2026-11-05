<?php

namespace Modules\Mikrotik\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Mikrotik\Entities\{Router, Lote, Ficha};

class FichasService
{
    public function __construct(private RouterFactory $factory, private HotspotService $hotspot) {}

    public function evento(Router $r, string $accion, bool $correcto, ?int $actor, ?int $ficha = null): void
    {
        DB::table('mt_eventos')->insert(['router_id' => $r->id, 'ficha_id' => $ficha, 'actor_id' => $actor,
            'accion' => $accion, 'correcto' => $correcto, 'created_at' => CarbonImmutable::now('UTC')]);
    }

    public function crear(Router $router, array $d, int $actor, bool $empleado = false): Lote
    {
        return DB::transaction(function () use ($router, $d, $actor, $empleado) {
            $modo = $empleado ? 'permanente' : 'primer_ingreso';
            if ((!$empleado && !in_array((int) $d['dias'], [1,2,7], true))
                || ($empleado && ((int) $d['dias'] !== 0 || (int) $d['cantidad'] !== 1 || empty(trim($d['visitante'] ?? ''))))) {
                throw ValidationException::withMessages(['dias' => 'Seleccione una vigencia válida o registre un empleado con nombre.']);
            }
            Router::whereKey($router->id)->lockForUpdate()->firstOrFail();
            $anterior = Lote::where('solicitud_uuid', $d['solicitud_uuid'])->first();
            if ($anterior) {
                if ($anterior->inicio_vigencia !== $modo || (int) $anterior->router_id !== $router->id || (int) $anterior->creado_por !== $actor
                    || (int) $anterior->dias !== (int) $d['dias'] || (int) $anterior->cantidad !== (int) $d['cantidad']
                    || (string) $anterior->visitante !== (string) ($d['visitante'] ?? '')) {
                    throw ValidationException::withMessages(['solicitud_uuid' => 'Este envío ya pertenece a otro lote. Recargue el formulario.']);
                }
                return $anterior;
            }
            $vence = null;
            $lote = Lote::create(['router_id' => $router->id, 'solicitud_uuid' => $d['solicitud_uuid'], 'dias' => $d['dias'],
                'cantidad' => $d['cantidad'], 'visitante' => $d['visitante'] ?? null, 'creado_por' => $actor, 'inicio_vigencia' => $modo, 'vence_at' => $vence]);
            for ($i = 0; $i < (int) $d['cantidad']; $i++) {
                Ficha::create(['router_id' => $router->id, 'lote_id' => $lote->id,
                    'usuario' => ($empleado ? 'E-' : 'V-').Str::upper(Str::random(12)), 'clave' => Str::random(12),
                    'estado' => 'pendiente', 'inicio_vigencia' => $modo, 'duracion_dias' => $empleado ? null : $d['dias'], 'vence_at' => $vence]);
            }
            $this->evento($router, 'crear_lote', true, $actor);
            return $lote;
        });
    }

    public function publicar(Lote $lote, int $actor, bool $verificarActivas = false): array
    {
        return DB::transaction(function () use ($lote, $actor, $verificarActivas) {
            $r = Router::whereKey($lote->router_id)->lockForUpdate()->firstOrFail();
            $fichas = $lote->fichas()->lockForUpdate()->get();
            $correctas = 0;
            $errores = 0;
            $api = null;
            $problema = null;
            try {
                $api = $this->factory->abrir($r);
                $this->hotspot->listo($api, $r);
            } catch (ErrorRouter $e) { $problema = $e->getMessage(); }
            foreach ($fichas as $f) {
                $f->setRelation('router', $r);
                if ($f->revocada_at || $f->vencida()) { continue; }
                if ($f->estado === 'activa' && !$verificarActivas) { $correctas++; continue; }
                try {
                    if ($problema) { throw new ErrorRouter($problema); }
                    $this->hotspot->publicar($api, $f);
                    $f->update(['estado' => 'activa', 'sincronizada_at' => CarbonImmutable::now('UTC'), 'error' => null]);
                    $this->evento($r, 'publicar_ficha', true, $actor, $f->id);
                    $correctas++;
                } catch (ErrorRouter $e) {
                    $f->update(['estado' => 'error', 'error' => $e->getMessage()]);
                    $this->evento($r, 'publicar_ficha', false, $actor, $f->id);
                    // Después de un timeout no se reutiliza un flujo API que podría estar desfasado.
                    $problema = 'Se interrumpió la publicación del lote. Reintente las fichas pendientes para confirmar su estado.';
                    $errores++;
                }
            }
            return compact('correctas', 'errores');
        });
    }

    public function eliminarFicha(int $id, int $actor): ?bool
    {
        $routerId = Ficha::whereKey($id)->value('router_id');
        if (!$routerId) { return null; }
        return DB::transaction(function () use ($id, $actor, $routerId) {
            $r = Router::whereKey($routerId)->lockForUpdate()->firstOrFail();
            $f = Ficha::whereKey($id)->lockForUpdate()->first();
            if (!$f || !in_array($f->estado, ['error','revocada'], true)) { return null; }
            $f->setRelation('router', $r);
            $accion = $f->estado === 'error' ? 'eliminar_ficha_error' : 'eliminar_ficha_revocada';
            try {
                $this->hotspot->bloquear($this->factory->abrir($r), $f);
                $f->update(['revocada_at'=>$f->revocada_at ?? CarbonImmutable::now('UTC'), 'estado'=>'revocada', 'error'=>null]);
                $this->evento($r, $accion, true, $actor, $f->id);
                // Conservar historial, UUID del lote y credenciales cifradas sin reactivación.
                $f->delete();
                return true;
            } catch (ErrorRouter $e) {
                $f->update(['error'=>$e->getMessage()]);
                $this->evento($r, $accion, false, $actor, $f->id);
                return false;
            }
        });
    }

    public function revocar(Ficha $f, int $actor): void
    {
        DB::transaction(function () use ($f, $actor) {
            $r = Router::whereKey($f->router_id)->lockForUpdate()->firstOrFail();
            $f = Ficha::whereKey($f->id)->lockForUpdate()->firstOrFail();
            $f->update(['estado' => 'revocacion_pendiente', 'revocada_at' => $f->revocada_at ?? CarbonImmutable::now('UTC')]);
            $this->evento($r, 'solicitar_revocacion', true, $actor, $f->id);
        });
        // Se conserva la solicitud incluso si la API está fuera de línea.
        $this->conciliar($f->router_id, $actor);
    }

    public function conciliar(int $routerId, ?int $actor = null): int
    {
        return DB::transaction(function () use ($routerId, $actor) {
            $r = Router::whereKey($routerId)->lockForUpdate()->firstOrFail();
            $fichas = $r->fichas()->whereNotIn('estado', ['revocada', 'vencida'])->where(function ($q) {
                $q->whereNotNull('revocada_at')->orWhere('vence_at', '<=', CarbonImmutable::now('UTC'))
                    ->orWhere(function ($v) {
                        $v->where('inicio_vigencia', 'primer_ingreso')->where(function ($publicada) {
                            $publicada->where('estado', 'activa')->orWhereNotNull('sincronizada_at')->orWhereNotNull('activada_at');
                        });
                    });
            })->orderByRaw('revocada_at IS NULL ASC')->lockForUpdate()->get();
            if ($fichas->isEmpty()) { return 0; }
            $api = null;
            try { $api = $this->factory->abrir($r); } catch (ErrorRouter $e) {}
            $pendientes = 0;
            foreach ($fichas as $f) {
                $f->setRelation('router', $r);
                try {
                    if (!$api) { throw new ErrorRouter('Router sin conexión. La revocación seguirá pendiente hasta confirmarla.'); }
                    if (!$f->revocada_at) {
                        if ($f->inicio_vigencia === 'primer_ingreso') { $this->hotspot->sincronizar($api, $f); }
                        if (!$f->vencida()) {
                            $f->update(['sincronizada_at' => CarbonImmutable::now('UTC'), 'error' => null]);
                            continue;
                        }
                    }
                    $this->hotspot->bloquear($api, $f);
                    $f->update(['estado' => $f->revocada_at ? 'revocada' : 'vencida', 'error' => null, 'sincronizada_at' => CarbonImmutable::now('UTC')]);
                    $this->evento($r, 'bloquear_ficha', true, $actor, $f->id);
                } catch (ErrorRouter $e) {
                    $f->update(['error' => $e->getMessage()]);
                    $this->evento($r, 'bloquear_ficha', false, $actor, $f->id);
                    $api = null;
                    $pendientes++;
                }
            }
            return $pendientes;
        });
    }
}



