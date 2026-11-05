<?php

namespace Modules\Mikrotik\Services;

use Modules\Mikrotik\Entities\Router;
use RouterOS\{Client, Query};

class RouterApi implements Conexion
{
    private Client $client;
    private ProtocoloApi $protocolo;

    public function __construct(Router $router)
    {
        $ssl = ['ciphers' => 'DEFAULT', 'verify_peer' => true, 'verify_peer_name' => true, 'allow_self_signed' => false];
        if ($ca = config('mikrotik_visitantes.ca_file')) { $ssl['cafile'] = $ca; }
        try {
            $this->client = new Client(['host' => $router->host, 'port' => $router->puerto,
                'user' => $router->usuario_api, 'pass' => $router->clave_api,
                'ssl' => $router->tls, 'ssl_options' => $ssl,
                'timeout' => 3, 'socket_timeout' => 10, 'attempts' => 1]);
            $this->protocolo = new ProtocoloApi(new FlujoApi($this->client->getSocket()));
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
            return $this->protocolo->intercambiar($q);
        } catch (ErrorRouter $e) {
            throw new ErrorRouter($e->getMessage().' Operación: '.$ruta.'.');
        } catch (\Throwable $e) {
            throw new ErrorRouter('No se completó la operación API '.$ruta.'. Revise los registros del router y vuelva a intentar.');
        }
    }
}
