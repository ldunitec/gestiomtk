@extends('adminlte::page')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            Nuevo Ticket
        </a>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="tablaTickets">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Departamento</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->numero_ticket }}</td>
                    <td>{{ $ticket->cliente->nombre }}</td>
                    <td>{{ $ticket->departamento->nombre }}</td>
                    <td>{{ $ticket->prioridad }}</td>
                    <td>{{ $ticket->estado }}</td>
                    <td>
                        <a href="{{ route('tickets.show',$ticket) }}"
                           class="btn btn-info btn-sm">
                           Ver
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

