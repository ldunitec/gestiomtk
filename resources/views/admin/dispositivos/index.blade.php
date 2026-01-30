@extends('adminlte::page')

@section('title', 'Dispositivos Conectados')

@section('content_header')
    <h1>Dispositivos Conectados a la Red</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="tabla-dispositivos" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>MAC</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Última Conexión</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#tabla-dispositivos').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.dispositivos.data') }}',
            columns: [
                { data: 'ip' },
                { data: 'mac' },
                { data: 'nombre' },
                { data: 'estado' },
                { data: 'ultima_conexion' }
            ]
        });
    });
</script>
@stop
