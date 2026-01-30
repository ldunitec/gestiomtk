<?php

namespace App\Servicios;

use Illuminate\Support\Facades\Http;
use Exception;

class AnvizService
{
    protected string $ip;
    protected string $user;
    protected string $pass;
    protected $cookies;

    public function __construct()
    {
        $this->ip   = config('services.anviz.ip');
        $this->user = config('services.anviz.user');
        $this->pass = config('services.anviz.pass');
    }

    /**
     * Login y obtención de cookie
     */
    public function login()
    {
        $response = Http::withOptions([
                'allow_redirects' => true,
            ])
            ->asJson()
            ->post("http://{$this->ip}/api/login", [
                'username' => $this->user,
                'password' => $this->pass,
            ]);

        if (!$response->successful()) {
            throw new Exception('Error al autenticar con el VF30');
        }

        $this->cookies = $response->cookies();
        return true;
    }

    /**
     * Obtener asistencia paginada
     */
    public function getAttendance(int $page = 1, int $pageSize = 100)
    {
        if (!$this->cookies) {
            $this->login();
        }

        $response = Http::withCookies($this->cookies, $this->ip)
            ->post("http://{$this->ip}/api/attendance/list", [
                'page'     => $page,
                'pageSize' => $pageSize,
            ]);

        if (!$response->successful()) {
            throw new Exception('Error al obtener asistencia del VF30');
        }

        return $response->json();
    }
}