<?php

namespace Modules\Produccion\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Produccion\Entities\Agenda;
use Modules\Produccion\Services\TodoistService;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Produccion\Imports\AgendaImport;
use App\Models\User;

class AgendaController extends Controller
{


    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            Excel::import(new AgendaImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Registros importados correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al importar: ' . $e->getMessage()
            ], 500);
        }
    }


    private function getColorByTipo(?string $tipo): string
    {
        $colors = [
            'Interno' => '#3788d8',
            'Taller'  => '#28a745',
            'Visita'  => '#ffc107',
            'Llamada' => '#6c757d'
        ];

        return $colors[$tipo] ?? '#6c757d';
    }

    public function index()
    {
        $events = Agenda::activos()
            ->whereNotNull('fecha_inicio')
            ->get()
            ->map(function ($event) {
                return [
                    'id'    => $event->id,
                    'title' => $event->titulo,
                    'start' => $event->fecha_inicio,
                    'end'   => $event->fecha_fin,
                    'color' => $event->color,
                    'extendedProps' => [
                        'tipo'             => $event->tipo,
                        'assigned_user_id' => $event->assigned_user_id
                    ]
                ];
            });

        $unassignedEvents = Agenda::whereNull('fecha_inicio')
            ->where('estado', 1)
            ->get();

        // 4 usuarios disponibles (IDs unificados)
        $usuarios = [
            ['id' => '85036051', 'name' => 'Luis Diaz'],
            ['id' => '10128774', 'name' => 'Celvyn Lozano'],
            ['id' => '52565829', 'name' => 'Harling Maradiaga'],
            ['id' => '49411725', 'name' => 'Kevin Godoy'],
        ];

        return view('produccion::admin.agendas.index', compact('events', 'unassignedEvents', 'usuarios'));
    }

   public function store(Request $request)
{
// dd($request);

    $request->validate([
        'title' => 'required|string|max:255',
        'tipo'  => 'nullable|string'
    ]);

    $tipo = $request->tipo ?? 'Llamada';

    // Si viene 'todos', vacío o no numérico, lo convertimos a null
    $assignedUserId = ($request->assigned_user_id === 'todos' || empty($request->assigned_user_id))
        ? null
        : (int) $request->assigned_user_id;

    $event = Agenda::create([
        'titulo'           => $request->title,
        'fecha_inicio'     => null,
        'fecha_fin'        => null,
        'color'            => $this->getColorByTipo($tipo),
        'usuario_id'       => Auth::id(),
        'assigned_user_id' => $assignedUserId,
        'estado'           => 1,
        'tipo'             => $tipo,
        'contacto'         => $request->contacto ?? null,
        'telefono'         => $request->telefono ?? null,
        'servicio'         => $request->servicio ?? null,
        'proyecto'         => $request->proyecto ?? null,
    ]);

    return response()->json([
        'success' => true,
        'event'   => $event
    ]);
}

    public function updateDate(Request $request, $id)
    {
        try {
            $event = Agenda::findOrFail($id);

            $start = $request->start ? Carbon::parse($request->start)->format('Y-m-d H:i:s') : null;

            $event->update([
                'fecha_inicio' => $start,
                'estado'       => $request->estado ?? 1,
            ]);

            // Sincronizar con Todoist si tiene fecha asignada
            if ($start) {
                $this->enviarATodoist($event);
            }

            return response()->json([
                'success' => true,
                'message' => 'Fecha actualizada correctamente',
                'event'   => $event
            ]);
        } catch (\Exception $e) {
            Log::error("Error en AgendaController::updateDate: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la fecha: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateDateItem(Request $request, Agenda $event)
    {
        // dd('update Date item');
        try {
            $fechaInput = $request->start ?? $request->fecha_inicio;
            $fecha = $fechaInput ? Carbon::parse($fechaInput)->format('Y-m-d H:i:s') : null;
            $tipo = $request->tipo ?? $event->tipo;

            $event->update([
                'titulo'           => $request->title ?? $event->titulo,
                'tipo'             => $tipo,
                'color'            => $this->getColorByTipo($tipo),
                'fecha_inicio'     => $fecha,
                'assigned_user_id' => $request->assigned_user_id ?? $event->assigned_user_id,
                'estado'           => $request->estado ?? 1,
            ]);

            // Sincronizar con Todoist si tiene fecha asignada
            if ($fecha) {
                $this->enviarATodoist($event);
            }

            return response()->json([
                'success' => true,
                'event'   => $event
            ]);

        } catch (\Exception $e) {
            Log::error("Error updateDateItem Agenda ID {$event->id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la actividad'
            ], 500);
        }
    }


public function enviarATodoist(Agenda $event)
{
    try {
        $fechaInicio = $event->fecha_inicio ? Carbon::parse($event->fecha_inicio) : null;
        if (!$fechaInicio) {
            return null;
        }

        $nombresUsuarios = [
            '8503605'  => 'Luis Diaz',
            '10128774' => 'Celvyn Lozano',
            '52565829' => 'Harling Maradiaga',
            '49411725' => 'Kevin Godoy'
        ];

        $todoistUserIds = config('services.todoist.users') ?? [
            '8503605'  => env('TODOIST_LD'),
            '49411725' => env('TODOIST_KG'),
            '52565829' => env('TODOIST_HM'),
            '10128774' => env('TODOIST_CL'),
        ];

        $projectId = config('services.todoist.project_id') ?? env('TODOIST_PROJECT_ID');
        $idLuisDiaz = $todoistUserIds['8503605'] ?? env('TODOIST_LD');
        
        $responsableNombre = $nombresUsuarios[$event->assigned_user_id] ?? 'Sin asignar';
        $responsableTodoistId = $todoistUserIds[$event->assigned_user_id] ?? null;

        $todoistService = app(TodoistService::class);

        // Definir a quiénes se les creará la tarea
        // Estructura: [ todoist_user_id => nombre_para_descripcion ]
        $destinatarios = [];

        if (!empty($responsableTodoistId)) {
            $destinatarios[$responsableTodoistId] = $responsableNombre;
        }

        // Siempre incluir a Luis Diaz (si no es él mismo el asignado)
        if (!empty($idLuisDiaz) && !isset($destinatarios[$idLuisDiaz])) {
            $destinatarios[$idLuisDiaz] = 'Luis Diaz (Supervisión)';
        }

        // --- CREACIÓN NUEVA ---
        if (empty($event->todoist_task_id)) {
            $createdTaskIds = [];

            foreach ($destinatarios as $assigneeId => $nombreAsignado) {
                $payload = [
                    'project_id'  => $projectId,
                    'title'       => $event->titulo. ','.$event->servicio,
                    'description' => "Ticket: {$event->secuencia_ticket}",
                    'due_date'    => $fechaInicio->format('Y-m-d'),
                    'assignee_id' => (string) $assigneeId,
                    'priority'    => 4,
                ];

                $res = $todoistService->createTask($payload);

                if ($res['success']) {
                    $createdTaskIds[] = (string) $res['data']['id'];
                } else {
                    Log::error("[Todoist] Error creando tarea para {$nombreAsignado}: " . ($res['error'] ?? 'Desconocido'));
                }
            }

            if (!empty($createdTaskIds)) {
                // Si es un solo ID guardamos el string directo, si son varios guardamos JSON
                $taskReference = count($createdTaskIds) === 1 ? $createdTaskIds[0] : json_encode($createdTaskIds);
                
                $event->update([
                    'todoist_task_id' => $taskReference
                ]);

                Log::info("[Todoist] Tareas creadas para asignado y Luis Diaz. IDs: " . json_encode($createdTaskIds));
                return $createdTaskIds;
            }

            return null;
        }

        // --- ACTUALIZACIÓN DE TAREAS EXISTENTES ---
        $existingTaskIds = [];
        $decoded = json_decode($event->todoist_task_id, true);
        if (is_array($decoded)) {
            $existingTaskIds = $decoded;
        } else {
            $existingTaskIds = [$event->todoist_task_id];
        }

        foreach ($existingTaskIds as $taskId) {
            $payloadUpdate = [
                'title'       => $event->titulo,
                'description' => "Categoría: {$event->tipo}\nAsignado a: {$responsableNombre}\nCreado desde el sistema de producción.",
                'due_date'    => $fechaInicio->format('Y-m-d'),
                'priority'    => 4,
            ];

            $todoistService->updateTask($taskId, $payloadUpdate);
        }

        Log::info("[Todoist] Tareas actualizadas en Todoist. IDs: " . json_encode($existingTaskIds));
        return true;

    } catch (\Exception $e) {
        Log::error("[Todoist] Excepción: " . $e->getMessage());
        return null;
    }
}


public function completarTarea(Request $request)
{
    $tokenTodoist = config('services.todoist.token'); // O tu token de API de Todoist
    $userLuisDiazId = "8503605"; // ID de Luis Díaz

    // 1. Marcar en tu base de datos local
    // $tarea = Tarea::find($request->id);
    // $tarea->completado = true;
    // $tarea->save();

    // 2. Opción A: Cerrar/Completar una tarea existente en Todoist por su task_id
    if ($request->has('todoist_task_id')) {
        $response = Http::withToken($tokenTodoist)
            ->post("https://api.todoist.com/rest/v2/tasks/{$request->todoist_task_id}/close");
    } 
    // 3. Opción B: Crear una nueva tarea ya asignada a Luis Díaz
    else {
        $response = Http::withToken($tokenTodoist)
            ->post('https://api.todoist.com/rest/v2/tasks', [
                'content' => $request->title ?? 'Tarea completada desde el calendario',
                'assignee_id' => $userLuisDiazId, // Asignado a Luis Díaz
                'due_string' => 'today',
            ]);

        // Si deseas cerrarla inmediatamente tras crearla:
        if ($response->successful()) {
            $taskId = $response->json('id');
            Http::withToken($tokenTodoist)
                ->post("https://api.todoist.com/rest/v2/tasks/{$taskId}/close");
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Tarea completada y sincronizada con Todoist'
    ]);
}

public function getEvents(Request $request)
    {
        $events = Agenda::whereNotNull('fecha_inicio')
            ->where('estado', 1)
            ->get();

        $data = $events->map(function ($event) {
            return [
                'id'    => $event->id,
                'title' => $event->titulo,
                'start' => $event->fecha_inicio,
                'end'   => $event->fecha_fin ?? null,
                'color' => $event->color ?? '#3788d8',
                'extendedProps' => [
                    'tipo'             => $event->tipo,
                    'assigned_user_id' => $event->assigned_user_id,
                    'contacto'         => $event->contacto,
                    'telefono'         => $event->telefono,
                    'servicio'         => $event->servicio,
                    'proyecto'         => $event->proyecto,
                ],
            ];
        });

        return response()->json($data);
    }
}