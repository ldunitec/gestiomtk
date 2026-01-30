<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agenda;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use App\Services\TodoistService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class AgendaController extends Controller
{
    // Mostrar calendario y tareas sin fecha
    public function index()
    {
        // Eventos ya agendados
        $events = Agenda::whereNotNull('fecha_inicio')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->titulo,
                    'start' => $event->fecha_inicio,
                    'end' => $event->fecha_fin,
                    'color' => $event->color ?? '#3788d8',
                ];
            });

        // Eventos sin fecha
        $unassignedEvents = Agenda::whereNull('fecha_inicio')
            ->where('estado', 1)->get();

        return view('admin.agenda.index', compact('events', 'unassignedEvents'));
    }



    // Crear evento sin fecha
    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255', 'tipo' => 'string']);
        $tipo = $request->tipo ?? 'Sin categoría';
        $colors = ['Taller' => '#3788d8', 'Visita' => '#28a745', 'Llamada' => '#ffc107', 'Sin categoría' => '#6c757d'];
        $color = $colors[$tipo] ?? '#6c757d';
        $event =
            Agenda::create([
                'titulo' => $request->title,
                'fecha_inicio' => null,
                'fecha_fin' => null,
                // 'color'        => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                'color' => $color,
                'usuario_id' => Auth::id(),
            ]);
        // enviar a Todoist
        $this->enviarATodoist($event);


        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'title' => $event->titulo,
                'start' => $event->fecha_inicio,
                'end' => $event->fecha_fin,
                'color' => $event->color,
            ]
        ]);


    }


    // Actualizar fecha al mover o arrastrar
    public function updateDate(Request $request, Agenda $event)
    {
        // Solo fecha YYYY-MM-DD
        $start = $request->start;

        $event->update([
            'fecha_inicio' => $start,
        ]);
        $this->enviarATodoist($event);
        // $this->enviarATodoistGrupo($titulo,$event);
        // enviar a Todoist

        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'title' => $event->titulo,
                'start' => $start,
                'color' => $event->color,
            ]
        ]);

    }

     public function updateDateItem(Request $request, Agenda $event)
    {
        // Solo fecha YYYY-MM-DD
        $start = $request->start;
        

        $event->update([
            'fecha_inicio' => $start,
        ]);
        // enviar a Todoist
        $this->enviarATodoist($event);

        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'title' => $event->titulo,
                'start' => $start,
                'color' => $event->color,
            ]
        ]);

    }

 


    private function enviarATodoist($event)
    {
        Log::info("Enviando tarea a Todoist...");
Log::info("Enviando tarea a Todoist...");

    $token = config('services.todoist.token');
    $projectId = config('services.todoist.project_id');
    $usuarios = config('services.todoist.usuarios');

    // Log::info("Token cargado: " . $token); 


        if (!$token) {
            Log::error('Todoist: TOKEN no definido en .env');
            return;
        }

        foreach ($usuarios as $usuarioId) {
            $response = Http::withToken($token)->post('https://api.todoist.com/rest/v2/tasks', [
                'content' => $event->titulo,
                'project_id' => $projectId,
                'assignee_id' => $usuarioId, // ✅ CORRECTO
                'due_datetime' => Carbon::parse($event->fecha_inicio . ' 10:00')->toIso8601String(),
                'priority' => 4, // prioridad alta
            ]);

            if ($response->failed()) {
                Log::error("❌ Error creando tarea para usuario {$usuarioId}: " . $response->body());
                continue;
            }

            $tarea = $response->json();
            Log::info("✅ Tarea creada en Todoist para usuario {$usuarioId}: " . json_encode($tarea));

            // Agregar recordatorio a las 08:00 AM
            Http::withToken($token)->post("https://api.todoist.com/rest/v2/reminders", [
                "task_id" => $tarea["id"],
                "due_datetime" => Carbon::parse($event->fecha_inicio . ' 08:00')->toIso8601String(),
                "type" => "absolute"
            ]);

            // Guardar el primer todoist_id en el evento
            if (!$event->todoist_id && isset($tarea['id'])) {
                $event->todoist_id = $tarea['id'];
                $event->save();
            }
            // log('Envio a:'+$event->todoist_id );
        }
    }

    public function update(Request $request, $id)
    {
        $event = Agenda::findOrFail($id);
        $event->update($request->all());
        return response()->json(['ok' => true]);
        }
        
            // Eliminar evento
    public function destroy(Agenda $event)
    {
        $event->delete();
        return response()->json(['success' => true]);
    }

}
