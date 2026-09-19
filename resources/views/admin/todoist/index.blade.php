@extends('adminlte::page')

@section('title', 'Todoist Laravel')

@section('content_header')
    <h1>Gestión de Todoist</h1>
    <hr>
@stop

@section('content')
<div class="container-fluid">

    <h2 class="mb-4">Enviar Actividad a Todoist</h2>

    {{-- Mensajes de estado --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario --}}
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Nueva Tarea</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.todoist.store') }}">
                @csrf

                <div class="row">
                    {{-- Título --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="content" class="form-control" value="{{ old('content') }}" required>
                    </div>

                    {{-- Fecha --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Vencimiento</label>
                        <input type="text" name="due_string" class="form-control" placeholder="tomorrow, next monday, today 5pm" value="{{ old('due_string') }}">
                    </div>
                </div>

                {{-- Descripción --}}
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    {{-- Prioridad --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prioridad</label>
                        <select name="priority" class="form-control">
                            <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Normal (P4)</option>
                            <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>Media (P3)</option>
                            <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>Alta (P2)</option>
                            <option value="4" {{ old('priority') == '4' ? 'selected' : '' }}>Urgente (P1)</option>
                        </select>
                    </div>

                    {{-- Selector de Proyectos Dinámico --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Proyecto en Todoist</label>
                        <select name="project_id" class="form-control">
                            <option value="">Bandeja de Entrada (Inbox)</option>
                            @foreach($projects as $project)
                                <option value="{{ $project['id'] }}" {{ old('project_id') == $project['id'] ? 'selected' : '' }}>
                                    {{ $project['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Selector de Usuarios Locales --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Asignar a Usuario (Local)</label>
                        <select name="assignee_id" class="form-control">
                            <option value="8503605">luis Diaz</option>
                            @foreach($users as $user)
                                {{-- Nota: En value deberías poner el ID de Todoist si lo tienes mapeado, ej: $user->todoist_id --}}
                                <option value="{{ $user->id }}" {{ old('assignee_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Enviar a Todoist
                </button>
            </form>
        </div>
    </div>

    {{-- Tabla de Tareas --}}
    <div class="card mt-4">
        <div class="card-header bg-dark">
            <h3 class="card-title">Lista de Tareas Activas</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>Tarea</th>
                        <th>Descripción</th>
                        <th width="120" class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td><strong>{{ $task['content'] ?? 'Sin título' }}</strong></td>
                        <td>{{ $task['description'] ?? '-' }}</td>
                        <td class="text-center">
                            @if(isset($task['id']))
                                <form method="POST" action="{{ route('admin.todoist.destroy', $task['id']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta tarea?')">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4">
                            <span class="text-muted">No hay tareas pendientes en Todoist</span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop