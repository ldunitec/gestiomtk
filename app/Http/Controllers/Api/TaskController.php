<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return Task::orderBy('due_date')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'assigned_to' => 'nullable|string',
            'due_date' => 'nullable|date',
            'color' => 'nullable|string',
        ]);

        $data['synced'] = false;

        $task = Task::create($data);
        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'content' => 'sometimes|string',
            'assigned_to' => 'sometimes|string',
            'due_date' => 'sometimes|date|nullable',
            'synced' => 'sometimes|boolean',
            'color' => 'sometimes|string|nullable',
        ]);

        $task->update($data);
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }

    // Endpoint para sincronizar tareas con Todoist
    public function sincronizar()
    {
        $tasks = Task::where('synced', false)->get();
        $token = config('services.todoist.token');

        $client = new \GuzzleHttp\Client();

        $sincronizadas = 0;

        foreach ($tasks as $task) {
            try {
                $response = $client->post('https://api.todoist.com/rest/v2/tasks', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'content' => $task->content,
                        'priority' => 4,
                        'due_datetime' => $task->due_date ? $task->due_date->toIso8601String() : null,
                    ],
                ]);

                if ($response->getStatusCode() == 200 || $response->getStatusCode() == 204) {
                    $task->synced = true;
                    $task->save();
                    $sincronizadas++;
                }
            } catch (\Exception $e) {
                // Log error si quieres
            }
        }

        return response()->json(['sincronizadas' => $sincronizadas]);
    }
}
