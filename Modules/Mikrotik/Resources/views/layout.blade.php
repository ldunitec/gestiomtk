@extends('adminlte::page')
@section('title', 'MikroTik · Visitantes')
@section('content_header')<h1>MikroTik <small class="text-muted">Hotspot y visitantes</small></h1>@stop
@section('content')
<nav class="d-flex flex-wrap mb-3" aria-label="MikroTik">
@can('mikrotik.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{ route('mt.index') }}">Resumen y routers</a>@endcan
@can('mikrotik.configurar')<a class="btn btn-outline-primary mr-2 mb-2" href="{{ route('mt.configuracion') }}">Configuración</a>@endcan
@can('mikrotik.fichas')<a class="btn btn-primary mr-2 mb-2" href="{{ route('mt.fichas') }}">Fichas de visitantes</a>@endcan
@can('mikrotik.sesiones')<a class="btn btn-outline-primary mr-2 mb-2" href="{{ route('mt.sesiones.routers') }}">Sesiones Hotspot</a>@endcan
</nav>
@if(session('ok'))<div class="alert alert-success" role="status">{{ session('ok') }}</div>@endif
@if(session('aviso'))<div class="alert alert-warning" role="alert">{{ session('aviso') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('mt_content')
<p class="text-muted small pb-3">Fechas y horas de Honduras. La vigencia comienza al crear la ficha.</p>
@stop
