<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TicketController extends Controller
{
    public function index()
    {
        $tecnicos = User::all(); // luego lo filtramos por rol
        return view('admin.tickets.index', compact('tecnicos'));
    }

    public function listar()
    {
        $tickets = Ticket::latest()->get();

        return datatables()->of($tickets)

            ->addColumn('estado', function ($t) {

                $vencido = now()->diffInHours($t->created_at) > ($t->sla_horas ?? 0);

                if ($vencido && $t->estado !== 'finalizado') {
                    return '<span class="badge bg-danger">SLA Vencido</span>';
                }

                return '<span class="badge bg-secondary">' . e($t->estado) . '</span>';
            })
            ->addColumn('sla', function ($t) {

                $horas = now()->diffInHours($t->created_at);
                $restante = ($t->sla_horas ?? 0) - $horas;

                if ($t->estado === 'finalizado') {
                    return '<span class="badge bg-success">Finalizado</span>';
                }

                if ($restante <= 0) {
                    return '<span class="badge bg-danger">Vencido</span>';
                }

                return "<span class='badge bg-warning tiempo' data-tiempo='{$restante}'>{$restante}h</span>";
            })

            ->addColumn('acciones', function ($t) {

                $id = $t->id;
                $pdfUrl = route('admin.tickets.pdf', $id);

                $botones = "
        <button class='btn btn-warning btn-sm editar' data-id='{$id}'>Editar</button>
        <button class='btn btn-danger btn-sm eliminar' data-id='{$id}'>Eliminar</button>
        <button class='btn btn-info btn-sm asignar' data-id='{$id}'>Asignar</button>
    ";

                if ($t->estado === 'pendiente') {
                    $botones .= "<button class='btn btn-success btn-sm iniciar' data-id='{$id}'>Iniciar</button>";
                }

                if ($t->estado === 'proceso') {
                    $botones .= "<button class='btn btn-dark btn-sm finalizar' data-id='{$id}'>Finalizar</button>";
                }

                $botones .= "
        <button class='btn btn-secondary btn-sm evidencias' data-id='{$id}'>Evidencias</button>
        <a href='{$pdfUrl}' target='_blank' class='btn btn-danger btn-sm'>PDF</a>
    ";

                return $botones;
            })
            ->rawColumns(['estado', 'acciones', 'sla'])
            ->make(true);
    }

    public function store(Request $request)
    {

        // dd($request);
        Ticket::create([
            'codigo' => 'TK-' . Str::upper(Str::random(6)),
            'cliente' => $request->cliente,
            'empresa' => $request->empresa,
            'equipo' => $request->equipo,
            'problema' => $request->problema,
            'prioridad' => $request->prioridad,
        ]);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        return Ticket::findOrFail($id);
    }

    public function update(Request $request)
    {
        $ticket = Ticket::findOrFail($request->id);

        $ticket->update($request->all());

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request)
    {
        Ticket::findOrFail($request->id)->delete();

        return response()->json(['success' => true]);
    }


    public function pdf($id)
    {
        $ticket = Ticket::with('asignacion.tecnico', 'evidencias')->findOrFail($id);

        $pdf = Pdf::loadView('admin.tickets.pdf', compact('ticket'));

        return $pdf->stream('reporte_ticket_' . $ticket->codigo . '.pdf');
    }

    public function dashboard()
    {
        // totales
        $total = Ticket::count();
        $pendientes = Ticket::where('estado', 'pendiente')->count();
        $proceso = Ticket::where('estado', 'en_proceso')->count();
        $finalizados = Ticket::where('estado', 'finalizado')->count();

        // SLA vencidos
        $sla_vencidos = Ticket::where('estado', '!=', 'finalizado')
            ->get()
            ->filter(function ($t) {
                return now()->diffInHours($t->created_at) > $t->sla_horas;
            })->count();

        // tickets por mes
        $meses = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $meses[] = Carbon::create()->month($i)->format('M');

            $data[] = Ticket::whereMonth('created_at', $i)->count();
        }

        // tickets por técnico
        $tecnicos = User::withCount('asignacion')->get();
$tecnicos = $tecnicos
    ->where('asignacion_count', '>', 0)
    ->sortByDesc('asignacion_count')
    ->values();
        return view('admin.tickets.dashboard', compact(
            'total',
            'pendientes',
            'proceso',
            'finalizados',
            'sla_vencidos',
            'meses',
            'data',
            'tecnicos'
        ));
    }



}