@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <table id="tablaViaticos" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Destino</th>
            <th>Vehículo</th>
            <th>KM</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
    </thead>
</table>


        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
$('#tablaViaticos').DataTable({
    ajax: "{{ route('viaticos.list') }}",
    columns: [
        { data: 'created_at' },
        { data: 'cliente' },
        { data: 'destino' },
        { data: 'vehiculo' },
        { data: 'km' },
        {
            data: 'total',
            render: d => 'L. ' + d
        },
        {
            data: 'id',
            render: id => `
                <a href="/viaticos/pdf/${id}" class="btn btn-danger btn-sm">PDF</a>
            `
        }
    ]
});

    </script>
@stop
