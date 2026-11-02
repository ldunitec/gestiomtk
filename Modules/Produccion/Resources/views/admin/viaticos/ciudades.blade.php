@extends('adminlte::page')
@section('title', 'Ciudades y kilometraje')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <h1>Producción · Ciudades y kilometraje</h1>
    <a href="{{ route('admin.viaticos.create') }}" class="btn btn-primary">Calcular viático</a>
</div>
@stop

@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
<div class="alert alert-info">
    Registra distancias por carretera de <strong>solo ida</strong>.
    Deja vacío un origen cuyo kilometraje todavía no conozcas; utiliza 0 para un viaje local.
    La fórmula de combustible calcula el regreso automáticamente.
</div>
<div class="card card-outline card-primary">
    <div class="card-header"><h3 class="card-title">{{ $editando ? 'Editar ciudad' : 'Agregar ciudad' }}</h3></div>
    <form method="POST" action="{{ $editando
        ? route('produccion.admin.viaticos.ciudades.update', $editando->id)
        : route('produccion.admin.viaticos.ciudades.store') }}">
        @csrf
        @if($editando) @method('PUT') @endif
        <div class="card-body row">
            <div class="col-md-4 form-group">
                <label for="nombre">Ciudad / destino</label>
                <input id="nombre" name="nombre" class="form-control" required maxlength="255"
                       value="{{ old('nombre', $editando->nombre ?? '') }}">
            </div>
            <div class="col-md-4 form-group">
                <label for="km_desde_tgu">Km de ida desde Tegucigalpa</label>
                <input id="km_desde_tgu" name="km_desde_tgu" type="number" step="0.01" min="0" max="999999.99"
                       class="form-control" value="{{ old('km_desde_tgu', $editando->km_desde_tgu ?? '') }}">
            </div>
            <div class="col-md-4 form-group">
                <label for="km_desde_sps">Km de ida desde San Pedro Sula</label>
                <input id="km_desde_sps" name="km_desde_sps" type="number" step="0.01" min="0" max="999999.99"
                       class="form-control" value="{{ old('km_desde_sps', $editando->km_desde_sps ?? '') }}">
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-success" type="submit">Guardar ciudad</button>
            @if($editando)<a class="btn btn-secondary" href="{{ route('admin.viaticos.ciudades') }}">Cancelar</a>@endif
        </div>
    </form>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Ciudad</th><th>Km desde Tegucigalpa</th><th>Km desde San Pedro Sula</th><th>Acciones</th></tr></thead>
            <tbody>
            @forelse($ciudades as $ciudad)
                <tr>
                    <td>{{ $ciudad->nombre }}</td>
                    <td>{{ $ciudad->km_desde_tgu !== null ? number_format($ciudad->km_desde_tgu, 2) : 'Sin configurar' }}</td>
                    <td>{{ $ciudad->km_desde_sps !== null ? number_format($ciudad->km_desde_sps, 2) : 'Sin configurar' }}</td>
                    <td><a class="btn btn-sm btn-warning" href="{{ route('admin.viaticos.ciudades', ['editar' => $ciudad->id]) }}">Editar</a></td>
                </tr>
            @empty
                <tr><td colspan="4">Todavía no hay ciudades. Agrega la primera en el formulario superior.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
