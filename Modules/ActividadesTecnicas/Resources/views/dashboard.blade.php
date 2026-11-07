@extends('actividades-tecnicas::layout')

@section('title', 'Control de actividades técnicas')

@section('content_header')
    <h1>Control de actividades técnicas</h1>
@stop

@section('at_content')
<div class="row">
    @foreach ([
        'Órdenes registradas' => $stats['total'],
        'Asignadas' => $stats['assigned'],
        'En ejecución' => $stats['in_progress'],
        'Completadas' => $stats['completed'],
        'Pendientes' => $stats['pending'],
    ] as $label => $value)
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner"><h3>{{ $value }}</h3><p>{{ $label }}</p></div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
            </div>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Órdenes recientes</h3>
        <div class="card-tools">
            @can('actividades-tecnicas.gestionar')<a href="{{ route('actividades-tecnicas.ordenes.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva orden
            </a>@endcan
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead><tr><th>Orden</th><th>Trabajo</th><th>Cliente</th><th>Estado</th><th>Programada</th></tr></thead>
            <tbody>
            @forelse ($recentOrders as $order)
                <tr>
                    <td><a href="{{ route('actividades-tecnicas.ordenes.show', $order) }}">{{ $order->order_number }}</a></td>
                    <td>{{ $order->title }}</td>
                    <td>{{ $order->client?->name ?? 'No asignado' }}</td>
                    <td>{{ $order->statusLabel() }}</td>
                    <td>{{ $order->scheduledLocal() ?? 'Sin fecha' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Todavía no hay órdenes registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
