@extends('mikrotik::layout')
@section('mt_content')
<div class="card"><div class="card-header">Consultar sesiones Hotspot</div><div class="card-body">
@forelse($routers as $router)<a href="{{ route('mt.sesiones', $router) }}" class="btn btn-outline-primary mr-2 mb-2">{{ $router->nombre }} · {{ $router->servidor_hotspot }}</a>@empty<p>No hay routers registrados.</p>@endforelse
</div></div>
@stop
