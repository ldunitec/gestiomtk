@extends('gym::layout')
@section('gym_content')
@if($r = session('resultado'))<div role="alert" class="alert alert-{{ $r['permitido'] ? 'success' : 'danger' }}"><h2 class="h4">{{ $r['permitido'] ? 'PERMITIDO' : 'RECHAZADO' }}</h2>{{ $r['motivo'] }} · {{ $r['codigo'] }}</div>@endif
<div class="card"><div class="card-header">Registrar entrada o salida</div><div class="card-body">
<form method="POST" action="{{ route('gym.accesos.registrar') }}">@csrf <input type="hidden" name="solicitud_id" value="{{ $solicitud }}">
<div class="form-group"><label for="codigo">Código del socio</label><input autofocus required autocomplete="off" id="codigo" name="codigo" maxlength="40" class="form-control form-control-lg" placeholder="GYM-…"></div>
<button name="movimiento" value="entrada" class="btn btn-success btn-lg mr-3">Entrada</button><button name="movimiento" value="salida" class="btn btn-outline-primary btn-lg">Salida</button>
</form><p class="text-muted mt-3">Puede escribir el código o usar un lector que funcione como teclado. Cada entrada autorizada consume una visita. La salida requiere una entrada abierta. Una consulta de validación por API no registra asistencia.</p></div></div>
@include('gym::tabla-accesos') {{ $accesos->links('pagination::bootstrap-4') }}
@stop
