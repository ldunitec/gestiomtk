<?php

namespace Modules\Gym\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Gym\Entities\{Socio, Membresia, Acceso, Asistencia};

class AccesoService
{
    public function evaluar(?Socio $socio): array
    {
        $hoy = MembresiaService::hoy();
        if (!$socio) {
            return ['permitido' => false, 'motivo' => 'Socio no encontrado'];
        }
        if ($socio->estado !== 'activo') {
            return ['permitido' => false, 'motivo' => 'Socio '.$socio->estado];
        }
        $m = $socio->membresias()->where('anulada', false)->where('inicio', '<=', $hoy)->where('fin', '>=', $hoy)->first();
        if (!$m) {
            return ['permitido' => false, 'motivo' => 'Sin membresía vigente'];
        }
        if ($m->pagado_centavos < $m->precio_centavos) {
            return ['permitido' => false, 'motivo' => 'Membresía pendiente de pago'];
        }
        if ($m->limite_visitas !== null && Asistencia::where('membresia_id', $m->id)->count() >= $m->limite_visitas) {
            return ['permitido' => false, 'motivo' => 'Visitas del plan agotadas'];
        }
        return ['permitido' => true, 'motivo' => 'Membresía vigente', 'membresia_id' => $m->id, 'vence' => $m->fin];
    }

    public function registrar(string $codigo, string $movimiento, string $uuid, int $actor): Acceso
    {
        return DB::transaction(function () use ($codigo, $movimiento, $uuid, $actor) {
            $socio = Socio::where('codigo', $codigo)->lockForUpdate()->first();
            $previo = Acceso::where('solicitud_id', $uuid)->first();
            if ($previo) {
                if ($previo->codigo !== $codigo || $previo->movimiento !== $movimiento || (int) $previo->operador_id !== $actor) {
                    throw ValidationException::withMessages(['solicitud_id' => 'El identificador ya pertenece a otro movimiento.']);
                }
                return $previo;
            }
            $ahora = CarbonImmutable::now('UTC');
            $abierta = $socio ? Asistencia::where('socio_id', $socio->id)->where('estado', 'abierta')->latest('entrada')->first() : null;
            $asistenciaId = null;
            if ($movimiento === 'salida') {
                $resultado = ['permitido' => (bool) $abierta, 'motivo' => $abierta ? 'Salida registrada' : 'Sin entrada abierta'];
                if ($abierta) {
                    $abierta->update(['salida' => $ahora, 'estado' => 'cerrada']);
                    $asistenciaId = $abierta->id;
                }
            } else {
                $resultado = $this->evaluar($socio);
                if ($abierta && $abierta->entrada->timezone(config('gym.timezone'))->toDateString() === MembresiaService::hoy()) {
                    $resultado = ['permitido' => false, 'motivo' => 'Ya tiene una entrada abierta'];
                }
                if ($resultado['permitido']) {
                    if ($abierta) {
                        $abierta->update(['estado' => 'sin_salida']);
                    }
                    $asistencia = Asistencia::create(['socio_id' => $socio->id, 'membresia_id' => $resultado['membresia_id'], 'entrada' => $ahora]);
                    $asistenciaId = $asistencia->id;
                    $resultado['motivo'] = 'Entrada autorizada';
                }
            }
            return Acceso::create(['solicitud_id' => $uuid, 'codigo' => $codigo, 'movimiento' => $movimiento,
                'socio_id' => $socio?->id, 'asistencia_id' => $asistenciaId, 'operador_id' => $actor,
                'permitido' => $resultado['permitido'], 'motivo' => $resultado['motivo'], 'momento' => $ahora]);
        });
    }
}
