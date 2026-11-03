@extends('mikrotik::layout')
@section('mt_content')
<div class="alert alert-info">Use un Hotspot ya configurado. La emisión requiere RouterOS 6.49.x o 7.x y hora/NTP correctos. API-SSL utiliza el certificado del router; para API sin TLS use únicamente su LAN o VPN.</div>
@foreach($routers as $r)
<div class="card"><div class="card-header"><strong>{{ $r->nombre }}</strong> · {{ $r->preparado_at ? 'Preparado para emitir fichas' : 'Pendiente de preparación' }}</div><div class="card-body">
<form method="POST" action="{{ route('mt.actualizar', $r) }}">@csrf @method('PUT') @include('mikrotik::form-router', ['router'=>$r, 'prefijo'=>'r-'.$r->id])</form>
<hr><div class="d-flex flex-wrap"><form method="POST" action="{{ route('mt.probar', $r) }}" class="mr-2 mb-2">@csrf <button class="btn btn-outline-info">Probar conexión</button></form><a class="btn btn-outline-warning mb-2" href="{{ route('mt.preparacion', $r) }}">Revisar preparación del Hotspot</a></div>
<p class="small text-muted">Una vez que existan fichas, registre otro router para cambiar su destino, transporte, Hotspot o portal.</p>
</div></div>
@endforeach
<div class="card"><div class="card-header">Agregar router</div><div class="card-body"><form method="POST" action="{{ route('mt.guardar') }}">@csrf @include('mikrotik::form-router', ['router'=>null,'prefijo'=>'nuevo'])</form></div></div>
@stop
