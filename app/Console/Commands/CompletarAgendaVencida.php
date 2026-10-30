<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Produccion\Entities\Agenda;
use Modules\Produccion\Services\TodoistService;

class CompletarAgendaVencida extends Command
{
    protected $signature = 'agenda:completar-vencidas {--simular : Mostrar las actividades sin modificarlas}';
    protected $description = 'Completa actividades pendientes anteriores a hoy y sus tareas vinculadas en Todoist.';

    public function handle()
    {
        // fecha_inicio en esta aplicación representa una fecha de calendario local.
        $hoy = Carbon::now('America/Tegucigalpa')->toDateString();
        $simular = (bool) $this->option('simular');
        $totales = ['completadas' => 0, 'sin_ids' => 0, 'errores' => 0, 'candidatas' => 0];
        $this->info(($simular ? 'SIMULACIÓN. ' : '') . 'Procesando actividades con fecha anterior a ' . $hoy);

        $service = null;
        if (!$simular) {
            try {
                $service = app(TodoistService::class);
            } catch (\Throwable $e) {
                Log::error('Agenda automática: servicio no disponible.', ['error' => $e->getMessage()]);
                $this->error('No se pudo iniciar Todoist. Revisa storage/logs/laravel.log.');
                return 1;
            }
        }

        Agenda::where('estado', 1)
            ->whereNotNull('fecha_inicio')
            ->whereDate('fecha_inicio', '<', $hoy)
            ->chunkById(50, function ($events) use ($hoy, $simular, $service, &$totales) {
                foreach ($events as $event) {
                    $totales['candidatas']++;
                    if ($simular) {
                        $ids = $this->ids($event);
                        $this->line('#' . $event->id . ' | ' . $event->fecha_inicio . ' | ' . $event->titulo
                            . ' | ' . (empty($ids) ? 'SIN IDs: quedará pendiente' : count($ids) . ' tarea(s) Todoist'));
                        continue;
                    }

                    try {
                        // Comparte la clave de lock de enviarATodoist() del controlador.
                        Cache::lock('agenda-todoist-' . $event->id, 600)->block(5, function () use ($event, $hoy, $service, &$totales) {
                            $event->refresh();
                            if ((int) $event->estado !== 1 || !$event->fecha_inicio
                                || Carbon::parse($event->fecha_inicio)->toDateString() >= $hoy) {
                                return;
                            }
                            $ids = $this->ids($event);
                            if (empty($ids)) {
                                $totales['sin_ids']++;
                                Log::warning('Agenda automática: sin IDs de Todoist; se conserva pendiente.', ['agenda_id' => $event->id]);
                                $this->warn('#' . $event->id . ': sin todoist_task_id.');
                                return;
                            }

                            // Verificar cada copia permite omitir las ya completadas en un intento parcial.
                            foreach ($ids as $taskId) {
                                $task = $service->getTask($taskId);
                                $this->verificar($task, $event->id, $taskId, 'consultar');
                                if (!is_array($task['data'] ?? null)) {
                                    throw new \RuntimeException('Todoist respondió sin datos de la tarea ' . $taskId);
                                }
                                $remote = $task['data'];
                                if (!empty($remote['is_completed']) || !empty($remote['checked'])) {
                                    continue;
                                }
                                // Evitar avanzar automáticamente una recurrencia al siguiente vencimiento.
                                if (!empty($remote['due']['is_recurring'])) {
                                    throw new \RuntimeException('La tarea ' . $taskId . ' es recurrente y requiere revisión manual.');
                                }
                                $this->verificar($service->completeTask($taskId), $event->id, $taskId, 'completar');
                            }

                            // Si otro usuario cambió fecha/responsable/estado durante el HTTP, no sobrescribirlo.
                            $query = Agenda::whereKey($event->id)->where('estado', 1)
                                ->where('fecha_inicio', $event->getRawOriginal('fecha_inicio'));
                            foreach (['assigned_user_id', 'updated_at'] as $field) {
                                $value = $event->getRawOriginal($field);
                                $value === null ? $query->whereNull($field) : $query->where($field, $value);
                            }
                            if ($query->update(['estado' => 2]) === 1) {
                                $totales['completadas']++;
                                Log::info('Agenda automática: actividad completada.', ['agenda_id' => $event->id, 'task_ids' => $ids]);
                                $this->info('#' . $event->id . ': completada en agenda y Todoist.');
                            } else {
                                throw new \RuntimeException('La actividad cambió durante el cierre; revisar su estado local y Todoist.');
                            }
                        });
                    } catch (\Throwable $e) {
                        $totales['errores']++;
                        Log::warning('Agenda automática: actividad pendiente de revisión o reintento.', [
                            'agenda_id' => $event->id, 'error' => $e->getMessage(),
                        ]);
                        $this->warn('#' . $event->id . ': ' . $e->getMessage());
                    }
                }
            });

        $this->line(json_encode($totales, JSON_UNESCAPED_UNICODE));
        if (!$simular) {
            Log::info('Agenda automática: resumen del cierre diario.', $totales + ['fecha_corte' => $hoy]);
        }
        return ($totales['errores'] > 0 || $totales['sin_ids'] > 0) ? 1 : 0;
    }

    private function ids(Agenda $event): array
    {
        $raw = $event->todoist_task_id;
        if ($raw instanceof \Illuminate\Support\Collection) {
            $raw = $raw->all();
        }
        if ($raw === null || $raw === '' || $raw === []) {
            return [];
        }
        $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);
        if (is_array($decoded)) {
            $values = array_values($decoded);
        } elseif ($decoded !== null && is_scalar($decoded)) {
            $values = [$decoded];
        } elseif ($raw === 'null') {
            return [];
        } else {
            $values = [$raw];
        }
        return array_values(array_unique(array_filter(array_map(function ($id) {
            return is_scalar($id) ? trim((string) $id) : '';
        }, $values), fn ($id) => $id !== '')));
    }

    private function verificar(array $response, $agendaId, $taskId, $accion): void
    {
        if (!($response['success'] ?? false)) {
            Log::warning('Agenda automática: error de Todoist.', [
                'agenda_id' => $agendaId, 'task_id' => $taskId, 'accion' => $accion,
                'status' => $response['status'] ?? null, 'respuesta' => $response['error'] ?? null,
            ]);
            throw new \RuntimeException('No se pudo ' . $accion . ' la tarea ' . $taskId
                . ' (HTTP ' . ($response['status'] ?? 'desconocido') . ').');
        }
    }
}
