<?php

namespace Modules\Mikrotik\Services;

use Carbon\CarbonImmutable;
use Modules\Mikrotik\Entities\Ficha;

class VigenciaFicha
{
    public static function inicial(Ficha $f): string
    {
        $valor = match ($f->inicio_vigencia) {
            'permanente' => 'E',
            'primer_ingreso' => 'P'.$f->duracion_dias,
            default => (string) $f->vence_at->timestamp,
        };
        return $f->router->marcador().':'.$valor;
    }

    // El router es la fuente del primer ingreso. Una vez registrado, es inmutable.
    public static function registrar(Ficha $f, string $comentario): void
    {
        $prefijo = $f->router->marcador().':';
        if (!str_starts_with($comentario, $prefijo)) { self::invalida(); }
        $valor = substr($comentario, strlen($prefijo));
        if ($f->inicio_vigencia !== 'primer_ingreso') {
            if ($comentario !== self::inicial($f)) { self::invalida(); }
            return;
        }
        if ($valor === 'P'.$f->duracion_dias && !$f->activada_at && !$f->vence_at) { return; }
        if (!preg_match('/^A([127]):(\d{10}):(\d{10})$/D', $valor, $m)
            || (int) $m[1] !== (int) $f->duracion_dias
            || (int) $m[2] < 1735689600
            || (int) $m[3] - (int) $m[2] !== (int) $f->duracion_dias * 86400
            || (int) $m[2] > CarbonImmutable::now('UTC')->timestamp + 90) { self::invalida(); }
        if ($f->activada_at || $f->vence_at) {
            if ($f->activada_at?->timestamp !== (int) $m[2] || $f->vence_at?->timestamp !== (int) $m[3]) { self::invalida(); }
            return;
        }
        $f->update(['activada_at' => CarbonImmutable::createFromTimestampUTC((int) $m[2]),
            'vence_at' => CarbonImmutable::createFromTimestampUTC((int) $m[3])]);
    }

    private static function invalida(): never
    {
        throw new ErrorRouter('La vigencia del usuario no corresponde a esta ficha o cambió. No se reinició su plazo.');
    }
}
