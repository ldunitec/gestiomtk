@extends('mikrotik::layout')
@section('mt_content')
<div class="card"><div class="card-header">Editar ficha {{ $ficha->usuario }}</div><div class="card-body">
<p>{{ $ficha->router->nombre }} · {{ $ficha->vigenciaVisible() }}</p>
<form method="POST" action="{{ route('mt.fichas.actualizar', $ficha) }}">
@csrf @method('PUT')
<div class="form-group"><label for="nombre">{{ $ficha->inicio_vigencia === 'permanente' ? 'Nombre del empleado' : 'Nombre / referencia del visitante' }}</label>
<input id="nombre" name="nombre" maxlength="100" class="form-control" @required($ficha->inicio_vigencia === 'permanente') value="{{ old('nombre', $ficha->nombreTitular()) }}"></div>
<p class="text-muted">Este nombre aparecerá en la ficha impresa. La edición conserva el usuario, la clave y la vigencia.</p>
<button class="btn btn-primary">Guardar cambios</button>
<a class="btn btn-outline-secondary" href="{{ route($ficha->inicio_vigencia === 'permanente' ? 'mt.empleados' : 'mt.fichas') }}">Volver al listado</a>
</form></div></div>
@stop
