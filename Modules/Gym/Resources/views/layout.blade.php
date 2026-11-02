@extends('adminlte::page')
@section('title', 'Gym')
@section('content_header')
<h1>Gym <small class="text-muted">Membresías y asistencia</small></h1>
@stop
@section('content')
<nav class="mb-3 d-flex flex-wrap" aria-label="Gym">
    @can('gym.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{ route('gym.index') }}">Resumen</a>@endcan
    @can('gym.administrar')
    <a class="btn btn-outline-primary mr-2 mb-2" href="{{ route('gym.socios') }}">Socios y membresías</a>
    <a class="btn btn-outline-primary mr-2 mb-2" href="{{ route('gym.planes') }}">Planes</a>
    @endcan
    @can('gym.accesos')<a class="btn btn-outline-primary mr-2 mb-2" href="{{ route('gym.accesos') }}">Control de acceso</a>@endcan
    @can('gym.reportes')<a class="btn btn-outline-primary mr-2 mb-2" href="{{ route('gym.reportes') }}">Reportes</a>@endcan
</nav>
@if(session('ok'))<div role="status" class="alert alert-success">{{ session('ok') }}</div>@endif
@if($errors->any())<div role="alert" class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('gym_content')
<p class="text-muted small pb-3">Horarios de Honduras · Importes en {{ config('gym.moneda') }}</p>
@stop
