<?php

namespace Modules\Mikrotik\Services;

use RouterOS\Interfaces\StreamInterface;

class FlujoApi implements StreamInterface
{
    private float $limite;

    /** @param resource $socket */
    public function __construct(private $socket)
    {
        $this->iniciar();
    }

    public function iniciar(): void
    {
        $this->limite = microtime(true) + 20;
    }

    private function comprobar(): void
    {
        if (!is_resource($this->socket)) {
            throw new ErrorRouter('La conexión API está cerrada. Vuelva a intentar la operación.');
        }
        if (microtime(true) >= $this->limite || (stream_get_meta_data($this->socket)['timed_out'] ?? false)) {
            throw new ErrorRouter('Se agotó el tiempo de espera de la API. Revise la ejecución del script y el registro del router.');
        }
    }

    public function read(int $length): string
    {
        if ($length <= 0) { throw new \InvalidArgumentException('La longitud de lectura debe ser positiva.'); }
        if ($length > 4194304) { throw new ErrorRouter('La respuesta API tiene una longitud no válida. Vuelva a conectar.'); }
        $datos = '';
        while (strlen($datos) < $length) {
            $this->comprobar();
            $fragmento = @fread($this->socket, $length - strlen($datos));
            $this->comprobar();
            if ($fragmento === false || $fragmento === '') {
                throw new ErrorRouter('La conexión API se interrumpió antes de recibir la respuesta completa. Reintente la operación.');
            }
            $datos .= $fragmento;
        }
        return $datos;
    }

    public function write(string $string, int $length = -1): int
    {
        $length = $length < 0 ? strlen($string) : min($length, strlen($string));
        $enviados = 0;
        while ($enviados < $length) {
            $this->comprobar();
            $n = @fwrite($this->socket, substr($string, $enviados, $length - $enviados));
            $this->comprobar();
            if ($n === false || $n === 0) {
                throw new ErrorRouter('La conexión API se interrumpió al enviar la operación. Reintente para confirmar su estado.');
            }
            $enviados += $n;
        }
        return $enviados;
    }

    public function close(): void
    {
        if (is_resource($this->socket)) { fclose($this->socket); }
        $this->socket = null;
    }
}
