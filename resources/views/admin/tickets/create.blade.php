


@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<form action="{{ route('tickets.store') }}" method="POST">
@csrf

<select name="cliente_id" class="form-control">
    @foreach($clientes as $cliente)
        <option value="{{ $cliente->id }}">
            {{ $cliente->nombre }}
        </option>
    @endforeach
</select>

<select name="departamento_id" class="form-control mt-2">
    @foreach($departamentos as $dep)
        <option value="{{ $dep->id }}">
            {{ $dep->nombre }}
        </option>
    @endforeach
</select>

<input type="text" name="asunto" class="form-control mt-2"
       placeholder="Asunto">

<textarea name="descripcion" class="form-control mt-2"></textarea>

<select name="prioridad" class="form-control mt-2">
    <option value="baja">Baja</option>
    <option value="normal">Normal</option>
    <option value="alta">Alta</option>
    <option value="critica">Crítica</option>
</select>

<button class="btn btn-success mt-3">
    Guardar
</button>

</form>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop
