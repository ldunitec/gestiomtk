<?php

namespace Modules\Mikrotik\Services;

use Modules\Mikrotik\Entities\Router;

class RouterFactory
{
    public function abrir(Router $router): Conexion
    {
        return new RouterApi($router);
    }
}
