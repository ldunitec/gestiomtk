<?php

namespace Modules\Mikrotik\Services;

class RespuestaApi
{
    public static function filas(array $raw): array
    {
        // La librería no lanza una excepción para todos los !trap al usar read().
        if (in_array('!trap', $raw, true) || in_array('!fatal', $raw, true) || !in_array('!done', $raw, true)) {
            throw new ErrorRouter('El router rechazó la operación. Revise permisos, servicios y registros del equipo.');
        }
        $filas = [];
        $actual = null;
        foreach ($raw as $palabra) {
            if ($palabra === '!re') {
                if ($actual !== null) { $filas[] = $actual; }
                $actual = [];
            } elseif ($palabra === '!done') {
                if ($actual !== null) { $filas[] = $actual; }
                break;
            } elseif ($actual !== null && preg_match('/^=([^=]+)=(.*)$/s', $palabra, $m)) {
                $actual[$m[1]] = $m[2];
            }
        }
        return $filas;
    }
}
