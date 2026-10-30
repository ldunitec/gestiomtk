<?php

namespace Modules\Produccion\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Produccion\Entities\Agenda;
use Modules\Produccion\Imports\AgendaImport;
use Modules\Produccion\Services\TodoistService;

class AgendaController extends Controller
{
    // IDs del selector local. Los IDs remotos se obtienen de services.todoist.users.
    private const USUARIOS = [
        '8503605' => 'Luis Diaz',
        '10128774' => 'Celvyn Lozano',
        '52565829' => 'Harling Maradiaga',
        '49411725' => 'Kevin Godoy',
    ];

    private function reglasResponsable()
    {
        // Se admite el antiguo ID local de Luis para registros existentes.
        return ['sometimes', 'nullable', Rule::in(array_merge(array_keys(self::USUARIOS), ['8503605', 'todos']))];
    }

    private function normalizarResponsable($value)
    {
        if ($value === null || $value === '' || $value === 'todos') {
            return null; // Convención de store(): null representa Todo el equipo.
        }
        return (string) $value === '8503605' ? 8503605 : (int) $value;
    }

    private function getColorByTipo(?string $tipo): string
    {
        return [
            'Interno' => '#3788d8',
            'Taller' => '#28a745',
            'Visita' => '#ffc107',
            'Llamada' => '#6c757d'
        ][$tipo] ?? '#6c757d';
    }

    private function eventoCalendario(Agenda $event)
    {
        return [
            'id' => $event->id,
            'title' => $event->titulo,
            'start' => $event->fecha_inicio,
            'end' => $event->fecha_fin,
            'color' => $event->color ?? $this->getColorByTipo($event->tipo),
            'extendedProps' => [
                'tipo' => $event->tipo,
                'assigned_user_id' => $this->normalizarResponsable($event->assigned_user_id),
                'contacto' => $event->contacto,
                'telefono' => $event->telefono,
                'servicio' => $event->servicio,
                'proyecto' => $event->proyecto,
            ],
        ];
    }

    public function index()
    {
        $events = Agenda::activos()->whereNotNull('fecha_inicio')->get()
            ->map(fn($event) => $this->eventoCalendario($event));
        $unassignedEvents = Agenda::whereNull('fecha_inicio')->where('estado', 1)->get();
        $usuarios = [];
        foreach (self::USUARIOS as $id => $name) {
            $usuarios[] = ['id' => (string) $id, 'name' => $name];
        }
        return view('produccion::admin.agendas.index', compact('events', 'unassignedEvents', 'usuarios'));
    }

    public function getEvents(Request $request)
    {
        return response()->json(Agenda::whereNotNull('fecha_inicio')->where('estado', 1)
            ->get()->map(fn($event) => $this->eventoCalendario($event)));
    }

    public function importExcel(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);
        try {
            Excel::import(new AgendaImport, $request->file('file'));
            return response()->json(['success' => true, 'message' => 'Registros importados correctamente.']);
        } catch (\Throwable $e) {
            Log::error('Error al importar agenda.', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'No se pudo importar el archivo. Revisa el registro del servidor.'], 500);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'tipo' => ['nullable', Rule::in(['Interno', 'Taller', 'Visita', 'Llamada'])],
            'assigned_user_id' => $this->reglasResponsable(),
            'contacto' => ['nullable', 'string'],
            'telefono' => ['nullable', 'string'],
            'servicio' => ['nullable', 'string'],
            'proyecto' => ['nullable', 'string'],
        ]);
        $tipo = $data['tipo'] ?? 'Llamada';
        $event = Agenda::create([
            'titulo' => $data['title'],
            'tipo' => $tipo,
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'color' => $this->getColorByTipo($tipo),
            'usuario_id' => Auth::id(),
            'assigned_user_id' => $this->normalizarResponsable($data['assigned_user_id'] ?? null),
            'estado' => 1,
            'contacto' => $data['contacto'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'servicio' => $data['servicio'] ?? null,
            'proyecto' => $data['proyecto'] ?? null,
        ]);
        return response()->json(['success' => true, 'event' => $event]);
    }

    public function updateDate(Request $request, $id)
    {
        $data = $request->validate([
            'start' => ['required', 'date'],
            'estado' => ['sometimes', 'integer', 'in:1,2'],
            'assigned_user_id' => $this->reglasResponsable(),
        ]);
        $event = Agenda::findOrFail($id);
        try {
            $changes = [
                'fecha_inicio' => Carbon::parse($data['start'])->format('Y-m-d H:i:s'),
                'estado' => $data['estado'] ?? 1,
            ];
            if (array_key_exists('assigned_user_id', $data)) {
                $changes['assigned_user_id'] = $this->normalizarResponsable($data['assigned_user_id']);
            }
            $event->update($changes);
        } catch (\Throwable $e) {
            Log::error('No se pudo guardar la fecha.', ['agenda_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'No se pudo guardar la fecha.'], 500);
        }
        return $this->respuestaAgendaTodoist($event, 'Fecha actualizada correctamente.');
    }

    // public function updateDateItem(Request $request, Agenda $event)
    // {
    //     $data = $request->validate([
    //         'title' => ['sometimes', 'required', 'string', 'max:255'],
    //         'tipo' => ['sometimes', 'required', Rule::in(['Interno', 'Taller', 'Visita', 'Llamada'])],
    //         'start' => ['sometimes', 'required', 'date'],
    //         'fecha_inicio' => ['sometimes', 'required', 'date'],
    //         'assigned_user_id' => $this->reglasResponsable(),
    //         'estado' => ['sometimes', 'integer', 'in:1,2'],
    //     ]);
    //     try {
    //         $tipo = $data['tipo'] ?? $event->tipo;
    //         $fecha = $data['start'] ?? $data['fecha_inicio'] ?? $event->fecha_inicio;
    //         $event->update([
    //             'titulo' => $data['title'] ?? $event->titulo,
    //             'tipo' => $tipo, 'color' => $this->getColorByTipo($tipo),
    //             'fecha_inicio' => $fecha ? Carbon::parse($fecha)->format('Y-m-d H:i:s') : null,
    //             'assigned_user_id' => $this->normalizarResponsable(array_key_exists('assigned_user_id', $data)
    //                 ? $data['assigned_user_id'] : $event->assigned_user_id),
    //             'estado' => $data['estado'] ?? $event->estado,
    //         ]);
    //     } catch (\Throwable $e) {
    //         Log::error('No se pudo guardar la actividad.', ['agenda_id' => $event->id, 'error' => $e->getMessage()]);
    //         return response()->json(['success' => false, 'message' => 'No se pudo guardar la actividad.'], 500);
    //     }
    //     return $this->respuestaAgendaTodoist($event, 'Actividad actualizada correctamente.');
    // }


    public function updateDateItem(Request $request, Agenda $event)
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'tipo' => ['sometimes', 'required', 'in:Interno,Taller,Visita,Llamada'],
            'start' => ['sometimes', 'required', 'date'],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'assigned_user_id' => ['sometimes', 'nullable', 'in:8503605,8503605,10128774,52565829,49411725,todos'],
            'estado' => ['sometimes', 'integer', 'in:1,2'],
            'contacto' => ['sometimes', 'nullable', 'string', 'max:255'],
            'telefono' => ['sometimes', 'nullable', 'string', 'max:255'],
            'servicio' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        try {
            if (array_key_exists('title', $data)) {
                $event->titulo = $data['title'];
            }
            if (array_key_exists('tipo', $data)) {
                $event->tipo = $data['tipo'];
                $event->color = $this->getColorByTipo($data['tipo']);
            }
            if (array_key_exists('start', $data) || array_key_exists('fecha_inicio', $data)) {
                $event->fecha_inicio = Carbon::parse($data['start'] ?? $data['fecha_inicio'])->format('Y-m-d H:i:s');
            }
            // Si no se envía fecha, conservar la existente (null para una pendiente).
            if (array_key_exists('assigned_user_id', $data)) {
                $assigned = $data['assigned_user_id'];
                $event->assigned_user_id = ($assigned === null || $assigned === '' || $assigned === 'todos')
                    ? null
                    : ((string) $assigned === '8503605' ? 8503605 : (int) $assigned);
            }
            foreach (['contacto', 'telefono', 'servicio'] as $field) {
                if (array_key_exists($field, $data)) {
                    $event->{$field} = $data[$field];
                }
            }
            if (array_key_exists('estado', $data)) {
                $event->estado = $data['estado'];
            }
            $event->save();
        } catch (\Throwable $e) {
            Log::error('No se pudo guardar la actividad.', [
                'agenda_id' => $event->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'No se pudo guardar la actividad. Revisa el registro del servidor.',
            ], 500);
        }

        // La edición de una pendiente no realiza llamadas HTTP a Todoist.
        if (!$event->fecha_inicio && (int) $event->estado !== 2) {
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Cambios guardados. La tarea sigue sin agendar.',
                'event' => $event->fresh(),
                'todoist' => ['success' => true, 'action' => 'sin_fecha', 'errors' => []],
                'warning' => null,
            ]);
        }

        return $this->respuestaAgendaTodoist($event, 'Actividad actualizada correctamente.');
    }

    public function completarTarea(Request $request)
    {
        // Completar mediante el ID LOCAL; no crear una tarea nueva para cerrarla.
        $data = $request->validate(['id' => ['required', 'integer']]);
        $event = Agenda::findOrFail($data['id']);
        try {
            $event->update(['estado' => 2]);
        } catch (\Throwable $e) {
            Log::error('No se pudo completar la actividad.', ['agenda_id' => $event->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'No se pudo completar la actividad.'], 500);
        }
        return $this->respuestaAgendaTodoist($event, 'Actividad completada correctamente.');
    }

    private function respuestaAgendaTodoist(Agenda $event, $message)
    {
        $sync = $this->enviarATodoist($event);
        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => $message,
            'event' => $event->fresh(),
            'todoist' => $sync,
            'warning' => $sync['success'] ? null
                : 'La actividad se guardó, pero Todoist no terminó de sincronizar. Revisa todoist.errors.',
        ]);
    }

    private function idsAgendaTodoist(Agenda $event)
    {
        $raw = $event->todoist_task_id;
        if ($raw instanceof \Illuminate\Support\Collection) {
            $raw = $raw->all();
        }
        if (empty($raw)) {
            return [];
        }
        $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);
        $ids = is_array($decoded) ? array_values($decoded) : [$raw];
        return array_values(array_unique(array_filter(array_map(function ($id) {
            return is_scalar($id) ? trim((string) $id) : '';
        }, $ids), fn($id) => $id !== '')));
    }

   private function guardarIdsAgendaTodoist(Agenda $event, array $ids)
{
    $ids = array_values(array_unique(array_filter(
        array_map(function ($id) {
            return trim((string) $id);
        }, $ids),
        function ($id) {
            return $id !== '';
        }
    )));

    $casts = $event->getCasts();

    $tieneCastJson = in_array(
        $casts['todoist_task_id'] ?? null,
        ['array', 'json', 'collection'],
        true
    );

    // Siempre guardar una lista JSON, incluso con un solo ID.
    $event->todoist_task_id = $tieneCastJson
        ? $ids
        : json_encode($ids, JSON_THROW_ON_ERROR);

    $event->save();
}

    private function verificarRespuesta(array $res, $accion)
    {
        if (!($res['success'] ?? false)) {
            Log::warning('Todoist: petición rechazada.', [
                'accion' => $accion,
                'status' => $res['status'] ?? null,
                'respuesta' => $res['error'] ?? null,
            ]);
            throw new \RuntimeException($accion . ' (HTTP ' . ($res['status'] ?? 'desconocido') . '). Revisa laravel.log.');
        }
    }

    public function enviarATodoist(Agenda $event)
    {


        try {
            return Cache::lock('agenda-todoist-' . $event->id, 600)->block(5, function () use ($event) {
                $event->refresh();
                $ids = $this->idsAgendaTodoist($event);
                $errors = [];
                // No necesita token si no hay nada que sincronizar.
                if ((int) $event->estado === 2 && empty($ids)) {
                    return ['success' => true, 'action' => 'sin_tareas', 'errors' => []];
                }
                if ((int) $event->estado !== 2 && !$event->fecha_inicio) {
                    return ['success' => true, 'action' => 'sin_fecha', 'errors' => []];
                }
                $service = app(TodoistService::class);
                if ((int) $event->estado === 2) {
                    foreach ($ids as $taskId) {
                        try {
                            $this->verificarRespuesta($service->completeTask($taskId), 'No se pudo completar la tarea ' . $taskId);
                        } catch (\Throwable $e) {
                            $errors[] = $e->getMessage();
                        }
                    }
                    return ['success' => empty($errors), 'action' => 'completar', 'errors' => $errors];
                }

                $names = self::USUARIOS;

// Convertir las claves configuradas a los IDs locales del calendario.
// Los valores siguen siendo los IDs reales de Todoist.
$configUsers = config('services.todoist.users', []);

$users = [
    '8503605' => $configUsers['ld'] ?? null,
    '10128774' => $configUsers['cl'] ?? null,
    '52565829' => $configUsers['hm'] ?? null,
    '49411725' => $configUsers['kg'] ?? null,
];

$projectId = config('services.todoist.project_id');
$luisId = $users['8503605'];
                $assigned = $this->normalizarResponsable($event->assigned_user_id);
              $localRecipients = ($assigned === null || $assigned === '' || $assigned === 'todos')
    ? ['8503605']
    : [(string) $assigned];
                if (!$luisId || !$projectId) {
                    throw new \RuntimeException('Falta configurar services.todoist.project_id o el ID Todoist de Luis.');
                }
                $recipients = [];

                // Crear una tarea para cada responsable seleccionado.
                foreach ($localRecipients as $localId) {
                    $todoistUserId = $users[$localId] ?? null;

                    if (empty($todoistUserId)) {
                        throw new \RuntimeException(
                            'Falta configurar el ID Todoist de '
                            . ($names[$localId] ?? 'responsable ' . $localId)
                            . '.'
                        );
                    }

                    $recipients[(string) $todoistUserId] =
                        $names[$localId] ?? 'Responsable';
                }

                // Agregar siempre a Luis como supervisor.
// Al usar el ID como clave, no se duplica si él es el responsable.
                if (empty($luisId)) {
                    throw new \RuntimeException(
                        'Falta configurar el ID Todoist de Luis Díaz.'
                    );
                }

                $recipients[(string) $luisId] =
                    $recipients[(string) $luisId] ?? 'Luis Diaz (Supervisión)';
                $available = [];
                foreach ($ids as $taskId) {
                    $res = $service->getTask($taskId);
                    $this->verificarRespuesta($res, 'No se pudo consultar la tarea ' . $taskId);
                    if (!is_array($res['data'] ?? null)) {
                        throw new \RuntimeException('Todoist respondió sin datos para la tarea ' . $taskId);
                    }
                    $task = $res['data'];
                    if (!empty($task['is_completed']) || !empty($task['checked'])) {
                        $this->verificarRespuesta($service->reopenTask($taskId), 'No se pudo reabrir la tarea ' . $taskId);
                    }
                    $available[$taskId] = (string) ($task['responsible_uid'] ?? $task['assignee_id'] ?? '');
                }

                $title = trim((string) $event->titulo);
                if (!empty($event->servicio)) {
                    $title .= ' — ' . $event->servicio;
                }
                // $responsable = $assigned === null ? 'Todo el equipo' : ($names[$assigned] ?? 'Sin asignar');
                // $description = "Ticket: {$event->secuencia_ticket}\nCategoría: {$event->tipo}\nResponsable: {$responsable}\nAgenda: #{$event->id}\nCreado desde el sistema de producción.";
                $dueDate = Carbon::parse($event->fecha_inicio)->format('Y-m-d');
                $chosen = [];
                // Primero reservar coincidencias, incluida la copia de supervisión.
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
                    $payload = [
                        'content' => $title,
                        // 'description' => $description . "\nDestinatario: {$name}",
                        'due_date' => $dueDate,
                        'assignee_id' => (string) $assigneeId,
                        'priority' => 4,
                    ];
                    try {
                        if (isset($chosen[$assigneeId])) {
                            $res = $service->updateTask($chosen[$assigneeId], $payload);
                            $this->verificarRespuesta($res, 'No se pudo actualizar la tarea de ' . $name);
                        } else {
                            $res = $service->createTask($payload + ['project_id' => (string) $projectId]);
                            $this->verificarRespuesta($res, 'No se pudo crear la tarea de ' . $name);
                            if (empty($res['data']['id'])) {
                                throw new \RuntimeException('Todoist respondió sin ID para ' . $name);
                            }
                            $ids[] = (string) $res['data']['id'];
                            // Conservar cada éxito parcial para el próximo intento.
                            $this->guardarIdsAgendaTodoist($event, $ids);
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Todoist: destinatario pendiente.', ['agenda_id' => $event->id, 'destinatario' => $name, 'error' => $e->getMessage()]);
                        $errors[] = $e->getMessage();
                        // Si falló la persistencia tras crear, detener para no crear más copias sin guardar.
                        if ($e instanceof \Illuminate\Database\QueryException) {
                            break;
                        }
                    }
                }
                if (empty($errors)) {
                    foreach ($available as $taskId => $owner) {
                        try {
                            $this->verificarRespuesta($service->completeTask($taskId), 'No se pudo archivar la copia ' . $taskId);
                            $ids = array_values(array_filter($ids, fn($id) => (string) $id !== (string) $taskId));
                            $this->guardarIdsAgendaTodoist($event, $ids);
                        } catch (\Throwable $e) {
                            $errors[] = $e->getMessage();
                        }
                    }
                }
                return ['success' => empty($errors), 'action' => 'sincronizar', 'errors' => $errors];
            });
        } catch (\Throwable $e) {
            Log::error('Todoist: sincronización incompleta.', ['agenda_id' => $event->id, 'error' => $e->getMessage()]);
            return ['success' => false, 'action' => 'error', 'errors' => [$e->getMessage()]];
        }
    }

    public function verificarCompletadasTodoist()
{
    try {
        return Cache::lock('agenda-verificar-todoist', 600)
            ->get(function () {
                $token = config('services.todoist.token');
                $projectId = (string) config('services.todoist.project_id');

                if (!$token || !$projectId) {
                    throw new \RuntimeException(
                        'Falta configurar el token o el proyecto de Todoist.'
                    );
                }

                $eventos = Agenda::where('estado', 1)
                    ->whereNotNull('todoist_task_id')
                    ->get()
                    ->filter(function ($evento) {
                        return !empty($this->idsAgendaTodoist($evento));
                    });

                if ($eventos->isEmpty()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'No hay actividades activas vinculadas a Todoist.',
                        'actualizadas' => 0,
                    ]);
                }

                $http = \Illuminate\Support\Facades\Http::withToken($token)
                    ->acceptJson()
                    ->connectTimeout(10)
                    ->timeout(30);

                $baseUrl = 'https://api.todoist.com/api/v1/';

                // Leer todas las páginas antes de modificar la base local.
                $leerPaginas = function (
                    string $endpoint,
                    array $parametros,
                    string $campo
                ) use ($http, $baseUrl) {
                    $items = [];
                    $cursores = [];

                    do {
                        $respuesta = $http->get(
                            $baseUrl . $endpoint,
                            $parametros
                        );

                        if (!$respuesta->successful()) {
                            throw new \RuntimeException(
                                'Todoist respondió HTTP '
                                . $respuesta->status()
                                . ' al consultar ' . $endpoint . '.'
                            );
                        }

                        $data = $respuesta->json();

                        if (
                            !is_array($data)
                            || !isset($data[$campo])
                            || !is_array($data[$campo])
                        ) {
                            throw new \RuntimeException(
                                'Todoist devolvió una respuesta inesperada.'
                            );
                        }

                        foreach ($data[$campo] as $item) {
                            $items[] = $item;
                        }

                        $cursor = $data['next_cursor'] ?? null;

                        if ($cursor) {
                            if (isset($cursores[$cursor])) {
                                throw new \RuntimeException(
                                    'Todoist repitió un cursor de paginación.'
                                );
                            }

                            $cursores[$cursor] = true;
                            $parametros['cursor'] = $cursor;
                        }
                    } while ($cursor);

                    return $items;
                };

                $hasta = Carbon::now('UTC');

                // Revisar desde la creación de la actividad más antigua.
                $desde = $eventos->min('created_at');

                if (!$desde) {
                    throw new \RuntimeException(
                        'Las actividades no tienen fecha de creación.'
                    );
                }

                $desde = Carbon::parse($desde)
                    ->utc()
                    ->startOfDay();

                $completadas = [];

                // Dividir el historial en intervalos menores de tres meses.
                while ($desde->lt($hasta)) {
                    $fin = $desde->copy()->addDays(89);

                    if ($fin->gt($hasta)) {
                        $fin = $hasta->copy();
                    }

                    $items = $leerPaginas(
                        'tasks/completed/by_completion_date',
                        [
                            'since' => $desde->toIso8601ZuluString(),
                            'until' => $fin->toIso8601ZuluString(),
                            'limit' => 200,
                        ],
                        'items'
                    );

                    foreach ($items as $item) {
                        if (
                            (string) ($item['project_id'] ?? '') === $projectId
                            && empty($item['is_deleted'])
                            && !empty($item['completed_at'])
                            && !empty($item['id'])
                        ) {
                            $completadas[(string) $item['id']] = true;
                        }
                    }

                    $desde = $fin;
                }

                // Consultar después las activas para excluir tareas reabiertas.
                $activas = [];

                foreach ($leerPaginas(
                    'tasks',
                    ['project_id' => $projectId, 'limit' => 200],
                    'results'
                ) as $item) {
                    if (!empty($item['id'])) {
                        $activas[(string) $item['id']] = true;
                    }
                }

                $confirmadas = array_diff_key($completadas, $activas);
                $actualizadas = 0;
                $ocupadas = 0;

                foreach ($eventos as $evento) {
                    // Compartir el bloqueo usado por enviarATodoist().
                    $resultado = Cache::lock(
                        'agenda-todoist-' . $evento->id,
                        600
                    )->get(function () use ($evento, $confirmadas) {
                        $evento->refresh();

                        if ((int) $evento->estado !== 1) {
                            return 0;
                        }

                        foreach ($this->idsAgendaTodoist($evento) as $taskId) {
                            if (isset($confirmadas[(string) $taskId])) {
                                // Actualización directa: no llamar a enviarATodoist().
                                return Agenda::whereKey($evento->id)
                                    ->where('estado', 1)
                                    ->update(['estado' => 2]);
                            }
                        }

                        return 0;
                    });

                    if ($resultado === false) {
                        $ocupadas++;
                    } else {
                        $actualizadas += $resultado;
                    }
                }

                return response()->json([
                    'success' => true,
                    'actualizadas' => $actualizadas,
                    'message' => "Actividades completadas: {$actualizadas}."
                        . ($ocupadas
                            ? " En sincronización: {$ocupadas}; vuelve a verificar."
                            : ''),
                ]);
            }) ?: response()->json([
                'success' => false,
                'message' => 'Ya hay una verificación en curso.',
            ], 409);
    } catch (\Throwable $e) {
        Log::error('Error al verificar completadas de Todoist.', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
}
