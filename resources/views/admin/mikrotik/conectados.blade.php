@extends('adminlte::page')

@section('title', 'Equipos Conectados')

@section('content_header')
<h1>Equipos Conectados</h1>
@stop

@section('content')
<table id="tablaConectados" class="table table-bordered table-striped">
    <thead class="bg-dark">
        <tr>
            <th>Nombre</th>
            <th>MAC</th>
            <th>IP</th>
            <th>Servidor</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($hosts as $h)
   
        <tr style="text-align: center">
            <td>{{ $h['host-name'] ?? '-' }}</td>
            <td>{{ $h['mac-address'] ?? '-' }}</td>
            <td>{{ $h['address'] ?? '-' }}</td>
            <td>{{ $h['active-server'] ?? '-' }}</td>
            <td>
                @if ( $h['status']=== 'bound')
                    <span class="badge badge-success">activo</span>
                @else
                    <span class="badge badge-danger">inactivo</span>
                @endif


            </td>
            
        </tr>
        @endforeach
    </tbody>
</table>
@stop

@section('js')
<script>
$(function () {
    $('#tablaConectados').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[4, 'asc']],
        language: {
            search: "Buscar",
            zeroRecords: "No hay equipos conectados"
        }
    });
});
</script>
@stop
