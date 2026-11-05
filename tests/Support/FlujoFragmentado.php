<?php

namespace Tests\Support;

/** Transporte binario de pruebas que entrega y acepta fragmentos cortos. */
class FlujoFragmentado
{
    public $context;
    public static string $entrada = '';
    public static string $salida = '';
    private int $posicion = 0;

    public function stream_open($path, $mode, $options, &$opened_path): bool { return true; }
    public function stream_read($count): string
    {
        $v = substr(self::$entrada, $this->posicion, min(3, $count));
        $this->posicion += strlen($v);
        return $v;
    }
    public function stream_write($data): int
    {
        $v = substr($data, 0, 2);
        self::$salida .= $v;
        return strlen($v);
    }
    public function stream_eof(): bool { return $this->posicion >= strlen(self::$entrada); }
    public function stream_stat(): array { return []; }
    public function stream_close(): void {}
}
