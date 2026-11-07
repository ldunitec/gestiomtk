@extends('actividades-tecnicas::layout')
@section('title', $order->order_number)
@section('content_header')<h1>{{ $order->order_number }} — {{ $order->title }}</h1>@stop
@section('at_content')
<div class="card"><div class="card-body">
    @if($order->agenda)
    <div class="alert alert-info">Actividad #{{ $order->agenda_id }} de Agenda de Producción · {{ $order->agenda->tipo }} ·
    {{ \Modules\Produccion\Http\Controllers\AgendaController::responsablesOrden()[(string)$order->agenda->assigned_user_id] ?? 'Todo el equipo' }}
    <a class="btn btn-sm btn-outline-primary ml-2" href="{{ route('produccion.admin.agendas.index', ['actividad'=>$order->agenda_id]) }}">Ver en agenda</a></div>
    @endif
    <p><strong>Estado:</strong> {{ $order->statusLabel() }}</p>
    <p><strong>Prioridad:</strong> {{ $order->priorityLabel() }}</p>
    <p><strong>Cliente:</strong> {{ $order->client?->name ?? 'No asignado' }}</p>
    <p><strong>Ubicación:</strong> {{ $order->location?->address ?? 'No asignada' }}</p>
    <p><strong>Programada:</strong> {{ $order->scheduledLocal() ?? 'Sin fecha' }}</p>
    <p><strong>Descripción:</strong><br>{{ $order->description ?? 'Sin descripción' }}</p>
    @can('actividades-tecnicas.gestionar')@if($order->status !== 'cancelled')<a href="{{ route('actividades-tecnicas.ordenes.edit', $order) }}" class="btn btn-warning">Editar</a>@endif @endcan
    <a href="{{ route('actividades-tecnicas.ordenes.index') }}" class="btn btn-secondary">Volver</a>

    @can('actividades-tecnicas.cancelar')
    @if($order->status === 'new')
    <details class="mt-3"><summary class="text-danger">Cancelar orden</summary>
    <form method="POST" action="{{ route('actividades-tecnicas.ordenes.destroy', $order) }}">@csrf @method('DELETE')
    <p>La orden quedará cancelada y conservará su historial.</p>
    <label class="d-block"><input required type="checkbox" name="confirmar" value="1"> Confirmo la cancelación de esta orden.</label>
    <button class="btn btn-outline-danger">Cancelar orden</button></form></details>
    @endif
    @endcan
</div></div>
<div class="card"><div class="card-header">Historial de estados</div><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Fecha (Honduras)</th><th>Estado</th><th>Operador</th><th>Nota</th></tr></thead><tbody>
@foreach($order->statusHistory->sortBy('id') as $entry)
<tr><td>{{ $entry->changed_at->copy()->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td><td>{{ $entry->to_status === 'new' ? 'Nueva' : ($entry->to_status === 'cancelled' ? 'Cancelada' : $entry->to_status) }}</td><td>{{ $entry->user?->name }}</td><td>{{ $entry->notes }}</td></tr>
@endforeach
</tbody></table></div></div>
@stop

