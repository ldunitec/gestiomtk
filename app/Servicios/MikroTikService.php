<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;

class MikroTikService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'host' => '192.168.88.1',   // IP de tu Mikrotik
            'user' => 'admin',          // usuario con permisos
            'pass' => '123456',         // contraseña
            'port' => 8728,             // puerto API (habilitar en mikrotik)
        ]);
    }

    // Crear usuario
    public function crearHotspotUser($username, $password, $tiempo)
    {
        $query = (new Query('/ip/hotspot/user/add'))
            ->equal('name', $username)
            ->equal('password', $password)
            ->equal('profile', 'default')   // puedes cambiar el perfil
            ->equal('limit-uptime', $tiempo); // ej: 1h, 1d, 30m

        return $this->client->query($query)->read();
    }

    // Listar usuarios
    public function listarHotspotUsers()
    {
        $query = new Query('/ip/hotspot/user/print');
        return $this->client->query($query)->read();
    }

    // Eliminar usuario
    public function eliminarHotspotUser($id)
    {
        $query = (new Query('/ip/hotspot/user/remove'))
            ->equal('.id', $id);

        return $this->client->query($query)->read();
    }
}
