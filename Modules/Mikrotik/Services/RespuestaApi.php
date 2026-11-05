<?php

namespace Modules\Mikrotik\Services;

class RespuestaApi
{
    public static function filas(array $raw): array
    {
        // La librería no lanza una excepción para todos los !trap al usar read().
        if (in_array('!fatal', $raw, true)) {
            throw new ErrorRouter('El router cerró la sesión API. Vuelva a conectar.');
        }
        if (in_array('!trap', $raw, true)) {
            $mensaje = implode("\n", array_filter($raw, fn ($v) => is_string($v) && str_starts_with($v, '=message=')));
            // Clasificar sin devolver mensajes arbitrarios que puedan contener credenciales.
            if (preg_match('/not enough permissions|permission denied|not permitted/i', $mensaje)) {
                throw new ErrorRouter('El usuario API no tiene permisos suficientes. Revise su grupo y los permisos del script.');
            }
            if (preg_match('/syntax error|expected end of command|expected closing|expected command name/i', $mensaje)) {
                $ubicacion = preg_match('/line\s+(\d+)(?:\s+column\s+(\d+))?/i', $mensaje, $m)
                    ? ' Línea '.$m[1].(isset($m[2]) ? ', columna '.$m[2] : '').'.' : '';
                throw new ErrorRouter('El router informó un error de sintaxis en el script.'.$ubicacion);
            }
            if (str_contains(strtolower($mensaje), 'no such item')) {
                throw new ErrorRouter('El objeto solicitado ya no existe en el router. Prepare nuevamente el Hotspot.');
            }
            throw new ErrorRouter('El router rechazó la operación. Revise permisos, servicios y registros del equipo.');
        }
        if (!in_array('!done', $raw, true)) {
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
