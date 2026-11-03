@extends('mikrotik::layout')
@section('mt_content')
<h2 class="h5">Sesiones actuales · {{ $router->nombre }}</h2>
<p>Desconectar cierra la sesión actual; revocar una ficha impide nuevos ingresos. Las acciones de este módulo se limitan a sus fichas de visitantes.</p>
<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Usuario</th><th>IP</th><th>MAC</th><th>Conectado</th><th>Bytes entrada / salida</th><th>Acción</th></tr></thead><tbody>
@forelse($sesiones as $s)<tr><td>{{ $s['user'] ?? '' }}</td><td>{{ $s['address'] ?? '' }}</td><td>{{ $s['mac-address'] ?? '' }}</td><td>{{ $s['uptime'] ?? '' }}</td><td>{{ $s['bytes-in'] ?? '' }} / {{ $s['bytes-out'] ?? '' }}</td><td>@if($f = $fichas->get($s['user'] ?? ''))<form method="POST" action="{{ route('mt.desconectar', $f) }}">@csrf <button class="btn btn-sm btn-outline-warning">Desconectar</button></form>@else<span class="text-muted">Usuario externo</span>@endif</td></tr>@empty<tr><td colspan="6">No hay sesiones activas.</td></tr>@endforelse
</tbody></table></div>
@stop
