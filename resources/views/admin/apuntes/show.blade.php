@extends('adminlte::page')

@section('title', 'Detalle Apunte')

@section('content_header')
    <h1><i class="fas fa-key"></i> Detalle Apunte</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <p><strong>Título:</strong> {{ $apunte->titulo }}</p>
        <p><strong>Tipo:</strong> {{ strtoupper($apunte->tipo) }}</p>
        <p><strong>IP:</strong> {{ $apunte->ip }}</p>
        <p><strong>URL:</strong> {{ $apunte->url }}</p>
        <p><strong>Usuario:</strong> {{ $apunte->usuario }}</p>

        @role('ADMINISTRADOR')
        <p>
            <strong>Contraseña:</strong>
            <input type="text"
                   class="form-control"
                   value="{{ Crypt::decryptString($apunte->password) }}"
                   readonly>
        </p>
        @endrole

        <p><strong>Descripción:</strong></p>
        <div class="alert alert-light">
            {{ $apunte->descripcion }}
        </div>

        <a href="{{ route('admin.apuntes.index') }}" class="btn btn-secondary">
            Volver
        </a>

    </div>
</div>

@stop
