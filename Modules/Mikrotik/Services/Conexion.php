<?php

namespace Modules\Mikrotik\Services;

interface Conexion
{
    public function ejecutar(string $ruta, array $atributos = [], array $filtros = []): array;
}
