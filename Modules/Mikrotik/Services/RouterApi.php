<?php

namespace Modules\Mikrotik\Services;

use Modules\Mikrotik\Entities\Router;
use RouterOS\{Client, Query};

class RouterApi implements Conexion
{
    private Client $client;

    public function __construct(Router $router)
    {
        $ssl = ['ciphers' => 'DEFAULT', 'verify_peer' => true, 'verify_peer_name' => true, 'allow_self_signed' => false];
        if ($ca = config('mikrotik_visitantes.ca_file')) { $ssl['cafile'] = $ca; }
        try {
            $this->client = new Client(['host' => $router->host, 'port' => $router->puerto,
                'user' => $router->usuario_api, 'pass' => $router->clave_api,
                'ssl' => $router->tls, 'ssl_options' => $ssl,
                'timeout' => 3, 'socket_timeout' => 5, 'attempts' => 1]);
        } catch (\Throwable $e) {
            throw new ErrorRouter('No se pudo conectar. Revise API, dirección, puerto, credenciales y certificado TLS.');
        }
    }

    public function ejecutar(string $ruta, array $atributos = [], array $filtros = []): array
    {
        try {
            $q = new Query($ruta);
            foreach ($atributos as $k => $v) { $q->equal($k, (string) $v); }
            foreach ($filtros as $k => $v) { $q->where($k, (string) $v); }
            return RespuestaApi::filas($this->client->query($q)->read(false));
        } catch (ErrorRouter $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ErrorRouter('La comunicación con el router no terminó correctamente. Puede reintentar sin crear otra ficha.');
        }
    }
}
