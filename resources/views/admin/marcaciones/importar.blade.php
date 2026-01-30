@extends('adminlte::page')

@section('content_header')
<h2 style="color: green; background-color: yellow;"><b>Completado</b></h2>
    <h1><b>Listado de Marcas </b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
           
<form action="{{ route('admin.marcaciones.importar') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="file" name="archivo" class="form-control mb-2" required>

    <button class="btn btn-success">
        📥 Importar marcaciones
    </button>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Empleado</th>
            <th>Fecha</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($asistencias as $a)
            @php
                $horaOficial = '08:06:00';
                $tarde = $a->entrada > $horaOficial;
            @endphp
            <tr>
                <td>{{ $a->empleado_id }}</td>
                <td>{{ $a->fecha }}</td>
                <td>{{ $a->entrada }}</td>
                <td>{{ $a->salida }}</td>
                <td>
                    {{ $tarde ? '⏰ Tarde' : '✅ A tiempo' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<button onclick="window.print()" class="btn btn-primary">
    🖨️ Imprimir
</button>


            <!-- /.card -->
        </div>
    </div>

  

    {{-- @include('admin.marcas.parciales.modal'); --}}

    </div>
    </div>


@stop

@section('css')
     @include('admin.marcas.parciales.css');
@stop

@section('js')
  {{-- @include('admin.marcas.parciales.js'); --}}
 
@stop
