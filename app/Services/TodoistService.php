<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TodoistService
{
    // IMPORTANTE: Cambiado a la API REST v2 oficial
    protected string $baseUrl = 'https://api.todoist.com/rest/v2';
    protected string $token;

    public function __construct()
    {
        $this->token = config('services.todoist.token');
    }

    /**
     * Obtener todas las tareas activas
     */
    public function getTasks(): array
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . '/tasks')
            ->json() ?? [];
    }

    /**
     * Obtener todos los proyectos de la cuenta
     */
    public function getProjects(): array
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . '/projects')
            ->json() ?? [];
    }

    /**
     * Crear una nueva tarea con Proyecto y Asignado opcional
     */
    public function createTask(array $data): array
    {
        $body = [
            'content'     => $data['content'],
            'description' => $data['description'] ?? '',
            'priority'    => (int)($data['priority'] ?? 1),
            'due_string'  => $data['due_string'] ?? null,
        ];

        // Si se seleccionó un proyecto, lo adjuntamos
        if (!empty($data['project_id'])) {
            $body['project_id'] = $data['project_id'];
        }

        // Si se seleccionó un encargado (ID de Todoist), lo adjuntamos
        if (!empty($data['assignee_id'])) {
            $body['assignee_id'] = $data['assignee_id'];
        }

        return Http::withToken($this->token)
            ->post($this->baseUrl . '/tasks', $body)
            ->throw()
            ->json();
    }

    /**
     * Eliminar tarea por ID
     */
    public function deleteTask($id): bool
    {
        return Http::withToken($this->token)
            ->delete($this->baseUrl . "/tasks/{$id}")
            ->successful();
    }
}