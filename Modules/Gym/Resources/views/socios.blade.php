@extends('gym::layout')
@section('gym_content')
<div class="card"><div class="card-header">Buscar socios</div><div class="card-body">
<form class="form-inline mb-3" method="GET"><label class="sr-only" for="q">Buscar</label><input id="q" class="form-control mr-2" name="q" maxlength="100" value="{{ request('q') }}" placeholder="Nombre, código o documento"><button class="btn btn-primary">Buscar</button></form>
<div class="table-responsive"><table class="table"><thead><tr><th>Código</th><th>Socio</th><th>Estado</th><th></th></tr></thead><tbody>
@forelse($socios as $s)<tr><td>{{ $s->codigo }}</td><td>{{ $s->nombre }}</td><td>{{ ucfirst($s->estado) }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('gym.socios.ver', $s) }}">Membresías / editar</a></td></tr>@empty<tr><td colspan="4">No hay socios.</td></tr>@endforelse
</tbody></table></div>{{ $socios->links('pagination::bootstrap-4') }}</div></div>
<div class="card"><div class="card-header">Nuevo socio</div><div class="card-body"><p class="text-muted">Se asignará un código único para registrar entradas y salidas.</p><form method="POST" action="{{ route('gym.socios.guardar') }}">@csrf @include('gym::form-socio')</form></div></div>
@stop
