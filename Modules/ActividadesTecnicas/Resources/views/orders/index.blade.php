@extends('actividades-tecnicas::layout')
@section('title', 'Órdenes de trabajo')
@section('content_header')
    <h1>Órdenes de trabajo</h1>
@stop
@section('at_content')
<div class="card">
    <div class="card-header">
        @can('actividades-tecnicas.gestionar')<a href="{{ route('actividades-tecnicas.ordenes.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Crear orden
        </a>@endcan
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered">
            <thead><tr><th>Orden</th><th>Trabajo</th><th>Cliente</th><th>Estado</th><th>Prioridad</th><th></th></tr></thead>
            <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td><td>{{ $order->title }}</td>
                    <td>{{ $order->client?->name ?? '—' }}</td>
                    <td>{{ $order->statusLabel() }}</td>
                    <td>{{ $order->priorityLabel() }}</td>
                    <td><a class="btn btn-xs btn-info" href="{{ route('actividades-tecnicas.ordenes.show', $order) }}">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No hay órdenes registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $orders->links('pagination::bootstrap-4') }}
    </div>
</div>
@stop
