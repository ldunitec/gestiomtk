@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-md-4">
        
   <form action="{{ route('produccion.admin.tasks.store') }}" method="POST">
    @csrf
    
    <div class="form-group">
        <label>Título de la Tarea</label>
        <input type="text" name="title" class="form-control" placeholder="Ej: Revisar inventario" required>
    </div>

    <div class="form-group">
        <label>Descripción</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    <div class="form-group">
        <label>ID Proyecto Todoist</label>
        <input type="text" name="project_id" class="form-control" required>
    </div>

    <div class="form-group">
        <label>ID Usuario Asignado (Opcional)</label>
        <input type="text" name="assignee_id" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Enviar a Todoist</button>
</form>
    
    </div>
</div>
@endsection
@section('js')


@endsection
