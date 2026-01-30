@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<h1>Backups de Base de Datos</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@elseif(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form action="{{ route('admin.backups.create') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-primary mb-3">Crear Backup</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Archivo</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($files as $file)
            <tr>
                <td>{{ basename($file) }}</td>
                <td>
                    <a href="{{ route('admin.backups.download', basename($file)) }}" class="btn btn-sm btn-success">Descargar</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop





