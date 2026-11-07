@extends('adminlte::page')
@section('title', 'Actividades técnicas')
@section('content_header')<h1>Actividades técnicas</h1>@stop
@section('content')
<nav class="d-flex flex-wrap mb-3" aria-label="Actividades técnicas">
<a class="btn btn-outline-primary mr-2 mb-2" href="{{ route('actividades-tecnicas.dashboard') }}">Tablero</a>
<a class="btn btn-outline-primary mr-2 mb-2" href="{{ route('actividades-tecnicas.ordenes.index') }}">Órdenes de trabajo</a>
@can('actividades-tecnicas.gestionar')<a class="btn btn-primary mb-2" href="{{ route('actividades-tecnicas.ordenes.create') }}">Nueva orden</a>@endcan
</nav>
@if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
@if(session('warning'))<div class="alert alert-warning" role="alert">{{ session('warning') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('at_content')
@stop
