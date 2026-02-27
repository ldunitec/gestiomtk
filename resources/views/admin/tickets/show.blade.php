

@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
   <h4>{{ $ticket->numero_ticket }}</h4>
<p><strong>Cliente:</strong> {{ $ticket->cliente->nombre }}</p>
<p><strong>Asunto:</strong> {{ $ticket->asunto }}</p>

<hr>

@foreach($ticket->respuestas as $respuesta)
    <div class="card mb-2">
        <div class="card-body">
            <strong>{{ $respuesta->usuario->name }}</strong>
            <p>{{ $respuesta->mensaje }}</p>
        </div>
    </div>
@endforeach
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop