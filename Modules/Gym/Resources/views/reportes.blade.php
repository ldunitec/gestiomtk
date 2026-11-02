@extends('gym::layout')
@section('gym_content')
<form class="row align-items-end mb-3" method="GET">
@foreach(['desde' => 'Desde','hasta' => 'Hasta','codigo' => 'Código de socio (opcional)'] as $campo => $label)
<div class="form-group col-md-3"><label for="{{ $campo }}">{{ $label }}</label><input class="form-control" id="{{ $campo }}" name="{{ $campo }}" type="{{ $campo === 'codigo' ? 'text' : 'date' }}" value="{{ request($campo) }}" @if($campo !== 'codigo') required @else maxlength="40" @endif></div>
@endforeach
<div class="form-group col-md-3"><button class="btn btn-primary">Consultar</button><button name="csv" value="1" class="btn btn-outline-success">CSV asistencia</button></div>
</form>
<p><strong>{{ $total }}</strong> asistencias · <strong>{{ $rechazos }}</strong> intentos rechazados. Las entradas sin salida no tienen tiempo de permanencia confirmado.</p>
<div class="card"><div class="card-header">Asistencia</div><div class="card-body"><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Socio</th><th>Entrada</th><th>Salida</th><th>Estado</th></tr></thead><tbody>
@forelse($asistencias as $a)<tr><td>{{ $a->socio->nombre }}<br><small>{{ $a->socio->codigo }}</small></td><td>{{ $a->entrada->timezone(config('gym.timezone'))->format('d/m/Y H:i:s') }}</td><td>{{ $a->salida?->timezone(config('gym.timezone'))->format('d/m/Y H:i:s') ?? '—' }}</td><td>{{ str_replace('_',' ', $a->estado) }}</td></tr>@empty<tr><td colspan="4">Sin asistencia en el período.</td></tr>@endforelse
</tbody></table></div>{{ $asistencias->links('pagination::bootstrap-4') }}</div></div>
<div class="card"><div class="card-header">Intentos rechazados</div><div class="card-body">@include('gym::tabla-accesos', ['accesos' => $denegados]) {{ $denegados->links('pagination::bootstrap-4') }}</div></div>
@stop
