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
            'Taller' => '#28a745',
            'Visita' => '#ffc107',
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
                    'id' => $event->id,
                    'title' => $event->titulo,
                    'start' => $event->fecha_inicio,
                    'end' => $event->fecha_fin,
                    'color' => $event->color,
                    'extendedProps' => [
                        'tipo' => $event->tipo,
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
            'tipo' => 'nullable|string'
        ]);

        $tipo = $request->tipo ?? 'Llamada';

        // Si viene 'todos', vacío o no numérico, lo convertimos a null
        $assignedUserId = ($request->assigned_user_id === 'todos' || empty($request->assigned_user_id))
            ? null
            : (int) $request->assigned_user_id;

        $event = Agenda::create([
            'titulo' => $request->title,
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'color' => $this->getColorByTipo($tipo),
            'usuario_id' => Auth::id(),
            'assigned_user_id' => $assignedUserId,
            'estado' => 1,
            'tipo' => $tipo,
            'contacto' => $request->contacto ?? null,
            'telefono' => $request->telefono ?? null,
            'servicio' => $request->servicio ?? null,
            'proyecto' => $request->proyecto ?? null,
        ]);

        return response()->json([
            'success' => true,
            'event' => $event
        ]);
    }


public function updateDate(Request $request, $id)
{
    $data = $request->validate([
        'start' => ['required', 'date'],
        'estado' => ['sometimes', 'integer', 'in:1,2'],
    ]);
    try {
        $event = Agenda::findOrFail($id);
        $event->update([
            'fecha_inicio' => Carbon::parse($data['start'])->format('Y-m-d H:i:s'),
            'estado' => $data['estado'] ?? 1,
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['success' => false, 'message' => 'Actividad no encontrada.'], 404);
    } catch (\Throwable $e) {
        Log::error('No se pudo guardar la fecha.', ['agenda_id' => $id, 'error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'No se pudo guardar la fecha.'], 500);
    }
    return $this->respuestaAgendaTodoist($event, 'Fecha actualizada correctamente.');
}

public function updateDateItem(Request $request, Agenda $event)
{
    dd($request);
    $data = $request->validate([
        'title' => ['sometimes', 'required', 'string', 'max:255'],
        'tipo' => ['sometimes', 'required', 'in:Interno,Taller,Visita,Llamada'],
        'start' => ['sometimes', 'required', 'date'],
        'fecha_inicio' => ['sometimes', 'required', 'date'],
        'assigned_user_id' => ['sometimes', 'required', 'in:8503605,10128774,52565829,49411725,todos'],
        'estado' => ['sometimes', 'integer', 'in:1,2'],
    ]);
    try {
        $tipo = $data['tipo'] ?? $event->tipo;
        $fechaInput = $data['start'] ?? $data['fecha_inicio'] ?? $event->fecha_inicio;
        $event->update([
            'titulo' => $data['title'] ?? $event->titulo,
            'tipo' => $tipo,
            'color' => $this->getColorByTipo($tipo),
            'fecha_inicio' => $fechaInput ? Carbon::parse($fechaInput)->format('Y-m-d H:i:s') : null,
            'assigned_user_id' => $data['assigned_user_id'] ?? $event->assigned_user_id,
            'estado' => $data['estado'] ?? $event->estado,
        ]);
    } catch (\Throwable $e) {
        Log::error('No se pudo guardar la actividad.', ['agenda_id' => $event->id, 'error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'No se pudo guardar la actividad.'], 500);
    }
    return $this->respuestaAgendaTodoist($event, 'Actividad actualizada correctamente.');
}

private function respuestaAgendaTodoist(Agenda $event, $message)
{
    $sync = $this->enviarATodoist($event);
    // success representa el guardado LOCAL. No devolver 500 ni revertir el
    // calendario si la fecha ya está guardada pero Todoist falló.
    return response()->json([
        'success' => true,
        'message' => $message,
        'event' => $event->fresh(),
        'todoist' => $sync,
        'warning' => $sync['success'] ? null : 'La actividad se guardó, pero Todoist no terminó de sincronizar. Reintenta con Actualizar.',
    ]);
}

private function idsAgendaTodoist(Agenda $event)
{
    // Compatible con un ID antiguo, un array JSON y un atributo cast a array.
    $raw = $event->todoist_task_id;
    if (empty($raw)) {
        return [];
    }
    $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);
    $ids = is_array($decoded) ? array_values($decoded) : [$raw];
    return array_values(array_unique(array_filter(array_map(function ($id) {
        return is_scalar($id) ? trim((string) $id) : '';
    }, $ids))));
}

private function guardarIdsAgendaTodoist(Agenda $event, array $ids)
{
    $ids = array_values(array_unique(array_map('strval', $ids)));
    // Mantener el formato de su implementación original; columna sin cast array.
    $event->update(['todoist_task_id' => count($ids) === 1 ? $ids[0] : json_encode($ids)]);
}

public function enviarATodoist(Agenda $event)
{
    try {
        // Serializa sincronizaciones de la MISMA actividad. Usar cache compartida
        // (Redis/database) si hay varios servidores. TTL superior al tiempo HTTP.
        return \Illuminate\Support\Facades\Cache::lock('agenda-todoist-' . $event->id, 300)->block(5, function () use ($event) {
            $event->refresh();
            $ids = $this->idsAgendaTodoist($event);
            $errors = [];
            $service = app(TodoistService::class);

            // Completar NO crea nuevas tareas, incluso si no hay fecha.
            if ((int) $event->estado === 2) {
                foreach ($ids as $taskId) {
                    try {
                        $res = $service->completeTask($taskId);
                        if (!($res['success'] ?? false)) {
                            Log::warning('Todoist: error al completar.', ['agenda_id' => $event->id, 'task_id' => $taskId, 'status' => $res['status'] ?? null]);
                            $errors[] = "No se pudo completar la tarea {$taskId}.";
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Todoist: excepción al completar.', ['agenda_id' => $event->id, 'task_id' => $taskId, 'error' => $e->getMessage()]);
                        $errors[] = "No se pudo completar la tarea {$taskId}.";
                    }
                }
                return ['success' => empty($errors), 'action' => empty($ids) ? 'sin_tareas' : 'completar', 'errors' => $errors];
            }
            if (!$event->fecha_inicio) {
                return ['success' => true, 'action' => 'sin_fecha', 'errors' => []];
            }

            $names = ['8503605' => 'Luis Diaz', '10128774' => 'Celvyn Lozano', '52565829' => 'Harling Maradiaga', '49411725' => 'Kevin Godoy'];
            $users = config('services.todoist.users') ?? [
                '8503605' => env('TODOIST_LD'), '49411725' => env('TODOIST_KG'),
                '52565829' => env('TODOIST_HM'), '10128774' => env('TODOIST_CL'),
            ];
            $projectId = config('services.todoist.project_id') ?: env('TODOIST_PROJECT_ID');
            $luisId = $users['8503605'] ?? null;
            $assigned = (string) $event->assigned_user_id;
            $localRecipients = $assigned === 'todos' ? array_keys($names) : [$assigned];
            $recipients = [];
            foreach ($localRecipients as $localId) {
                if (empty($users[$localId])) {
                    throw new \RuntimeException('Falta configurar el ID Todoist de ' . ($names[$localId] ?? 'el responsable') . '.');
                }
                $recipients[(string) $users[$localId]] = $names[$localId] ?? 'Responsable';
            }
            if (!$luisId || !$projectId) {
                throw new \RuntimeException('Falta configurar el proyecto o el ID Todoist de Luis Diaz.');
            }
            $recipients[(string) $luisId] = $recipients[(string) $luisId] ?? 'Luis Diaz (Supervisión)';

            // Leer el responsable real de cada ID legado, sin asumir que el primer
            // ID es el técnico: la primera creación pudo fallar parcialmente.
            $available = [];
            foreach ($ids as $taskId) {
                $res = $service->getTask($taskId);
                if (!($res['success'] ?? false) || !is_array($res['data'] ?? null)) {
                    throw new \RuntimeException("No se pudo consultar la tarea {$taskId}; no se crearán copias para reemplazarla.");
                }
                $task = $res['data'];
                $available[$taskId] = (string) ($task['responsible_uid'] ?? $task['assignee_id'] ?? '');
            }

            $title = trim((string) $event->titulo);
            if (!empty($event->servicio)) {
                $title .= ' — ' . $event->servicio;
            }
            $responsable = $assigned === 'todos' ? 'Todo el equipo' : ($names[$assigned] ?? 'Sin asignar');
            $description = "Ticket: {$event->secuencia_ticket}\nCategoría: {$event->tipo}\nResponsable: {$responsable}\nAgenda: #{$event->id}\nCreado desde el sistema de producción.";
            $dueDate = Carbon::parse($event->fecha_inicio)->format('Y-m-d');

            // Reservar primero coincidencias exactas para proteger la copia de Luis.
            $chosen = [];
            foreach ($recipients as $assigneeId => $name) {
                foreach ($available as $taskId => $owner) {
                    if ((string) $owner === (string) $assigneeId) {
                        $chosen[$assigneeId] = $taskId;
                        unset($available[$taskId]);
                        break;
                    }
                }
            }
            foreach ($recipients as $assigneeId => $name) {
                if (!isset($chosen[$assigneeId]) && !empty($available)) {
                    $taskId = array_key_first($available);
                    $chosen[$assigneeId] = $taskId;
                    unset($available[$taskId]);
                }
            }

            foreach ($recipients as $assigneeId => $name) {
                $payload = ['title' => $title, 'description' => $description . "\nDestinatario: {$name}", 'due_date' => $dueDate, 'assignee_id' => (string) $assigneeId, 'priority' => 4];
                try {
                    if (isset($chosen[$assigneeId])) {
                        $res = $service->updateTask($chosen[$assigneeId], $payload);
                    } else {
                        $res = $service->createTask($payload + ['project_id' => (string) $projectId]);
                        if (($res['success'] ?? false) && !empty($res['data']['id'])) {
                            $taskId = (string) $res['data']['id'];
                            $ids[] = $taskId;
                            // Guardar cada creación inmediatamente para conservar
                            // éxitos parciales y no repetirlos en el siguiente intento.
                            $this->guardarIdsAgendaTodoist($event, $ids);
                            $chosen[$assigneeId] = $taskId;
                        } elseif ($res['success'] ?? false) {
                            throw new \RuntimeException('Todoist respondió sin ID de tarea.');
                        }
                    }
                    if (!($res['success'] ?? false)) {
                        Log::warning('Todoist: error al sincronizar destinatario.', ['agenda_id' => $event->id, 'destinatario' => $name, 'status' => $res['status'] ?? null]);
                        $errors[] = "No se pudo sincronizar a {$name}.";
                    }
                } catch (\Throwable $e) {
                    Log::warning('Todoist: excepción al sincronizar destinatario.', ['agenda_id' => $event->id, 'destinatario' => $name, 'error' => $e->getMessage()]);
                    $errors[] = "No se pudo sincronizar a {$name}.";
                }
            }

            // Si se reduce el equipo (p. ej. Todos -> un técnico), archivar las
            // copias sobrantes SOLO cuando los destinatarios actuales están listos.
            if (empty($errors)) {
                foreach ($available as $taskId => $owner) {
                    $res = $service->completeTask($taskId);
                    if ($res['success'] ?? false) {
                        $ids = array_values(array_filter($ids, fn ($id) => (string) $id !== (string) $taskId));
                        $this->guardarIdsAgendaTodoist($event, $ids);
                    } else {
                        $errors[] = "No se pudo archivar la copia anterior {$taskId}.";
                    }
                }
            }
            return ['success' => empty($errors), 'action' => 'sincronizar', 'errors' => $errors];
        });
    } catch (\Throwable $e) {
        Log::error('Todoist: sincronización incompleta.', ['agenda_id' => $event->id, 'error' => $e->getMessage()]);
        return ['success' => false, 'action' => 'error', 'errors' => ['No se pudo terminar la sincronización. Revisa el registro del servidor.']];
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
                'id' => $event->id,
                'title' => $event->titulo,
                'start' => $event->fecha_inicio,
                'end' => $event->fecha_fin ?? null,
                'color' => $event->color ?? '#3788d8',
                'extendedProps' => [
                    'tipo' => $event->tipo,
                    'assigned_user_id' => $event->assigned_user_id,
                    'contacto' => $event->contacto,
                    'telefono' => $event->telefono,
                    'servicio' => $event->servicio,
                    'proyecto' => $event->proyecto,
                ],
            ];
        });

        return response()->json($data);
    }
}