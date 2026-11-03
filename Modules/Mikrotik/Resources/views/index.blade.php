@extends('mikrotik::layout')
@section('mt_content')
<div class="row">
@foreach(['Routers registrados' => $routers->count(), 'Fichas activas y vigentes' => $activas, 'Fichas pendientes / con error' => $pendientes] as $nombre => $n)<div class="col-md-4"><div class="small-box bg-info"><div class="inner"><h3>{{ $n }}</h3><p>{{ $nombre }}</p></div></div></div>@endforeach
</div>
<div class="row">@forelse($routers as $router)
<div class="col-md-6"><div class="card"><div class="card-header"><h2 class="h5 mb-0">{{ $router->nombre }}</h2></div><div class="card-body">
<p>{{ $router->host }} · Hotspot: {{ $router->servidor_hotspot }} · Wi-Fi: {{ $router->ssid }}</p>
<p>Última comprobación: {{ $router->verificado_at?->timezone(config('mikrotik_visitantes.timezone'))->format('d/m/Y H:i') ?? 'Sin comprobar' }} · {{ $router->preparado_at ? 'Preparado' : 'Pendiente de preparación' }}</p>
<a class="btn btn-sm btn-outline-primary" href="{{ route('mt.estado', $router) }}">Consultar estado actual</a>
@can('mikrotik.sesiones')<a class="btn btn-sm btn-outline-primary" href="{{ route('mt.sesiones', $router) }}">Sesiones Hotspot</a>@endcan
</div></div></div>
@empty<div class="col"><div class="alert alert-info">Registre su router desde Configuración para comenzar.</div></div>@endforelse</div>
<div class="card"><div class="card-header">Historial reciente</div><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Fecha</th><th>Router</th><th>Acción</th><th>Operador</th><th>Resultado</th></tr></thead><tbody>
@foreach($eventos as $e)<tr><td>{{ \Carbon\Carbon::parse($e->created_at, 'UTC')->timezone(config('mikrotik_visitantes.timezone'))->format('d/m/Y H:i:s') }}</td><td>{{ $e->nombre }}</td><td>{{ str_replace('_',' ', $e->accion) }}</td><td>{{ $e->actor_id ?? 'Sistema' }}</td><td>{{ $e->correcto ? 'Confirmado' : 'Pendiente / error' }}</td></tr>@endforeach
</tbody></table></div></div>
@stop
