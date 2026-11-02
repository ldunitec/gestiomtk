<?php

namespace Modules\Gym\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Gym\Entities\{Socio, Plan, Membresia, Pago};

class MembresiaService
{
    public static function hoy(): string
    {
        return CarbonImmutable::now(config('gym.timezone'))->toDateString();
    }

    public static function centavos(string $importe): int
    {
        if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/D', $importe)) {
            throw ValidationException::withMessages(['importe' => 'Ingrese un monto positivo con un máximo de dos decimales.']);
        }
        [$entero, $decimal] = array_pad(explode('.', $importe), 2, '0');
        return ((int) $entero * 100) + (int) str_pad($decimal, 2, '0');
    }

    public function crear(Socio $socio, int $planId, string $inicio, string $abono, ?int $actor): Membresia
    {
        return DB::transaction(function () use ($socio, $planId, $inicio, $abono, $actor) {
            Socio::whereKey($socio->id)->lockForUpdate()->firstOrFail();
            $plan = Plan::whereKey($planId)->lockForUpdate()->firstOrFail();
            if (!$plan->activo) {
                throw ValidationException::withMessages(['plan_id' => 'El plan está inactivo.']);
            }
            $fin = CarbonImmutable::parse($inicio)->addDays($plan->dias - 1)->toDateString();
            if ($socio->membresias()->where('anulada', false)->where('inicio', '<=', $fin)->where('fin', '>=', $inicio)->exists()) {
                throw ValidationException::withMessages(['inicio' => 'Las fechas coinciden con otra membresía. Seleccione una fecha posterior a su vencimiento.']);
            }
            $centavos = self::centavos($abono);
            if ($centavos > $plan->precio_centavos) {
                throw ValidationException::withMessages(['abono' => 'El pago no puede superar el precio.']);
            }
            $m = Membresia::create(['socio_id' => $socio->id, 'plan_id' => $plan->id,
                'plan_nombre' => $plan->nombre, 'inicio' => $inicio, 'fin' => $fin,
                'precio_centavos' => $plan->precio_centavos, 'pagado_centavos' => $centavos,
                'limite_visitas' => $plan->limite_visitas, 'creado_por' => $actor]);
            if ($centavos > 0) {
                Pago::create(['membresia_id' => $m->id, 'centavos' => $centavos, 'creado_por' => $actor]);
            }
            return $m;
        });
    }

    public function pagar(Membresia $m, string $importe, ?string $referencia, ?int $actor): void
    {
        DB::transaction(function () use ($m, $importe, $referencia, $actor) {
            Socio::whereKey($m->socio_id)->lockForUpdate()->firstOrFail();
            $m = Membresia::whereKey($m->id)->lockForUpdate()->firstOrFail();
            $centavos = self::centavos($importe);
            if ($m->anulada || $centavos <= 0 || $centavos > $m->precio_centavos - $m->pagado_centavos) {
                throw ValidationException::withMessages(['importe' => 'Revise el saldo: no se admiten pagos en membresías anuladas, pagos en cero ni sobrepagos.']);
            }
            Pago::create(['membresia_id' => $m->id, 'centavos' => $centavos, 'referencia' => $referencia, 'creado_por' => $actor]);
            $m->increment('pagado_centavos', $centavos);
        });
    }
}
