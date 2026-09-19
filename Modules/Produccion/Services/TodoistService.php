<?php

namespace Modules\Produccion\Services;

use Illuminate\Support\Facades\Http;

class TodoistService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        // URL base oficial /api/v1/
        $this->baseUrl = rtrim(config('services.todoist.url', 'https://api.todoist.com/api/v1/'), '/') . '/';
        $this->token   = config('services.todoist.token');

        if (!$this->token) {
            throw new \Exception("Token de Todoist no configurado en .env o config/services.php");
        }
    }

    /**
     * Cliente HTTP base
     */
    protected function client()
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->asJson();
    }

    /**
     * Crear tarea
     */
    public function createTask(array $data)
    {
        $url = $this->baseUrl . 'tasks';

        $payload = array_filter([
            'content'     => $data['title'] ?? $data['content'] ?? null,
            'description' => $data['description'] ?? null,
            'project_id'  => $data['project_id'] ?? null,
            'due_date'    => $data['due_date'] ?? null,
            'due_string'  => $data['due'] ?? null,
            'assignee_id' => $data['assignee_id'] ?? null,
            'priority'    => $data['priority'] ?? null,
        ], fn($value) => !is_null($value));

        $response = $this->client()->post($url, $payload);

        return $this->handleResponse($response);
    }

    /**
     * Obtener tareas
     */
    public function getTasks()
    {
        $response = $this->client()->get($this->baseUrl . 'tasks');

        return $this->handleResponse($response);
    }

    /**
     * Obtener proyectos
     */
    public function getProjects()
    {
        $response = $this->client()->get($this->baseUrl . 'projects');

        return $this->handleResponse($response);
    }

    /**
     * Actualizar tarea
     */
    public function updateTask($taskId, array $data)
    {
        $url = $this->baseUrl . "tasks/{$taskId}";

        $payload = array_filter([
            'content'     => $data['title'] ?? $data['content'] ?? null,
            'description' => $data['description'] ?? null,
            'due_date'    => $data['due_date'] ?? null,
            'due_string'  => $data['due'] ?? null,
            'assignee_id' => $data['assignee_id'] ?? null,
            'priority'    => $data['priority'] ?? null,
        ], fn($value) => !is_null($value));

        $response = $this->client()->post($url, $payload);

        return $this->handleResponse($response);
    }

    /**
     * Completar tarea
     */
    public function completeTask($taskId)
    {
        $response = $this->client()->post($this->baseUrl . "tasks/{$taskId}/close");

        return $this->handleResponse($response);
    }

    /**
     * Eliminar tarea
     */
    public function deleteTask($taskId)
    {
        $response = $this->client()->delete($this->baseUrl . "tasks/{$taskId}");

        return $this->handleResponse($response);
    }

    /**
     * Manejo centralizado de respuestas
     */
    protected function handleResponse($response)
    {
        if ($response->failed()) {
            return [
                'success' => false,
                'status'  => $response->status(),
                'error'   => $response->body(),
            ];
        }

        return [
            'success' => true,
            'data'    => $response->json(),
        ];
    }
}