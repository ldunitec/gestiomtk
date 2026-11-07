@extends('actividades-tecnicas::layout')
@section('title', 'Editar orden')
@section('content_header')<h1>Editar {{ $order->order_number }}</h1>@stop
@section('at_content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('actividades-tecnicas.ordenes.update', $order) }}">
    @csrf @method('PUT')
    <div class="form-group"><label for="title">Descripción breve *</label><input id="title" name="title" maxlength="180" class="form-control" required value="{{ old('title', $order->title) }}"></div>
    <div class="form-group"><label for="description">Descripción</label><textarea id="description" maxlength="10000" name="description" class="form-control">{{ old('description', $order->description) }}</textarea></div>
    <div class="form-group"><label for="priority">Prioridad</label><select id="priority" name="priority" class="form-control">
        @foreach (['low'=>'Baja','normal'=>'Normal','high'=>'Alta','urgent'=>'Urgente'] as $key=>$label)
            <option value="{{ $key }}" @selected(old('priority', $order->priority) === $key)>{{ $label }}</option>
        @endforeach
    </select></div>
    <div class="form-group"><label for="scheduled_at">Fecha programada (hora de Honduras)</label><input type="datetime-local" id="scheduled_at" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', $order->scheduledLocal('Y-m-d\TH:i')) }}"></div>
    <button class="btn btn-primary">Actualizar</button>
</form>
</div></div>
@stop
