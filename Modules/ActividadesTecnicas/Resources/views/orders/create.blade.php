@extends('actividades-tecnicas::layout')
@section('title', 'Nueva orden de trabajo')
@section('content_header')<h1>Nueva orden de trabajo</h1>@stop
@section('at_content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('actividades-tecnicas.ordenes.store') }}">
    @csrf
    <div class="form-group"><label for="title">Descripción breve *</label><input id="title" name="title" class="form-control" required maxlength="255" value="{{ old('title') }}"></div>
    <div class="form-group"><label for="description">Descripción del trabajo</label><textarea id="description" maxlength="10000" name="description" class="form-control" rows="4">{{ old('description') }}</textarea></div>
    <div class="form-group"><label for="priority">Prioridad *</label>
        <select id="priority" name="priority" class="form-control" required>
            @foreach (['low'=>'Baja','normal'=>'Normal','high'=>'Alta','urgent'=>'Urgente'] as $key=>$label)
                <option value="{{ $key }}" @selected(old('priority','normal') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group"><label for="scheduled_at">Fecha programada (hora de Honduras)</label><input type="datetime-local" id="scheduled_at" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}"></div>
    <div class="row">
        <div class="form-group col-md-6"><label for="agenda_tipo">Tipo en agenda</label><select id="agenda_tipo" name="agenda_tipo" class="form-control">
        @foreach(['Interno','Taller','Visita','Llamada'] as $tipo)<option value="{{ $tipo }}" @selected(old('agenda_tipo', isset($order) ? ($order->agenda?->tipo ?? 'Visita') : 'Visita') === $tipo)>{{ $tipo }}</option>@endforeach
        </select></div>
        <div class="form-group col-md-6"><label for="agenda_responsable">Responsable en agenda</label><select id="agenda_responsable" name="agenda_responsable" class="form-control"><option value="todos">Todo el equipo</option>
        @foreach(\Modules\Produccion\Http\Controllers\AgendaController::responsablesOrden() as $id=>$nombre)<option value="{{ $id }}" @selected((string)old('agenda_responsable', isset($order) ? $order->agenda?->assigned_user_id : null) === (string)$id)>{{ $nombre }}</option>@endforeach
        </select></div>
    </div>
    <p class="small text-muted">La orden aparecerá en la agenda. Si no tiene fecha, quedará en «Sin agendar».</p>
    <button class="btn btn-primary">Guardar orden</button>
    <a href="{{ route('actividades-tecnicas.ordenes.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
</div></div>
@stop


