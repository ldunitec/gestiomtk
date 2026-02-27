@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="row">
    <div class="col md-12">
        <table id="tablaCiudades" class="table table-bordered">
    <thead>
        <tr>
            <th>Ciudad</th>
            <th>KM TGU</th>
            <th>KM SPS</th>
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
    $('#tablaCiudades').DataTable({
    ajax: "{{ route('ciudades.list') }}",
    columns: [
        { data: 'nombre' },
        { data: 'km_desde_tgu' },
        { data: 'km_desde_sps' },
        {
            data: null,
            render: d => `
              <button class="btn btn-warning btn-sm editar" data-id="${d.id}">✏️</button>
              <button class="btn btn-danger btn-sm eliminar" data-id="${d.id}">🗑️</button>
            `
        }
    ]
});

    </script>
@stop