@extends('adminlte::page')

@section('title', 'Calcular Viáticos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0 text-dark"><i class="fas fa-calculator text-primary mr-2"></i>Calcular Viáticos</h1>
        <a href="{{ route('admin.viaticos.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Volver al listado
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">

    {{-- Alerta de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="icon fas fa-check mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.viaticos.store') }}" method="POST">
        @csrf

        {{-- Bloque 1: Configuración de Tarifas Base --}}
        <div class="card card-outline card-secondary shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-sliders-h mr-1 text-secondary"></i> Parámetros y Tarifas Base
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-4 col-lg-2-4 mb-3">
                        <label class="small font-weight-bold">Combustible ($/L o gal)</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-gas-pump"></i></span></div>
                            <input type="number" step="0.01" name="precio_combustible" class="form-control" value="{{ old('precio_combustible', 120) }}" required>
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-2-4 mb-3">
                        <label class="small font-weight-bold">Tarifa Peaje Prom.</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-road"></i></span></div>
                            <input type="number" step="0.01" name="precio_peaje" class="form-control" value="{{ old('precio_peaje', 22) }}" required>
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-2-4 mb-3">
                        <label class="small font-weight-bold">Hotel / Noche</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-bed"></i></span></div>
                            <input type="number" step="1" name="precio_hotel" class="form-control" value="{{ old('precio_hotel', 1300) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-2-4 mb-3">
                        <label class="small font-weight-bold">Alimentación / Día</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-utensils"></i></span></div>
                            <input type="number" step="1" name="precio_alimentacion" class="form-control" value="{{ old('precio_alimentacion', 500) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-2-4 mb-3">
                        <label class="small font-weight-bold">Gasto Extra Base</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-coins"></i></span></div>
                            <input type="number" step="1" name="extra" class="form-control" value="{{ old('extra', 500) }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bloque 2: Información del Viaje --}}
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-route mr-1 text-primary"></i> Información de la RUTA y Personal
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="ciudad" class="font-weight-bold">Ciudad / Destino</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span></div>
                            <input type="text" id="ciudad" name="ciudad" class="form-control" placeholder="Ej. Monterrey, N.L." value="{{ old('ciudad') }}" required>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="distancia_km" class="font-weight-bold">Distancia Total (km)</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-tachometer-alt"></i></span></div>
                            <input type="number" id="distancia_km" name="distancia_km" class="form-control" placeholder="0" value="{{ old('distancia_km') }}" required>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="dias" class="font-weight-bold">Días de Viaje</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-day"></i></span></div>
                            <input type="number" step="1" id="dias" name="dias" class="form-control" value="{{ old('dias', 1) }}" min="1" required>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="noches" class="font-weight-bold">Noches de Hospedaje</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-moon"></i></span></div>
                            <input type="number" step="1" id="noches" name="noches" class="form-control" value="{{ old('noches', 0) }}" min="0" required>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="peajes" class="font-weight-bold">Cantidad de Peajes</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-archway"></i></span></div>
                            <input type="number" step="1" id="peajes" name="peajes" class="form-control" value="{{ old('peajes', 0) }}" min="0" required>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="cantidad_tecnicos" class="font-weight-bold">Cantidad de Técnicos</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-users"></i></span></div>
                            <input type="number" id="cantidad_tecnicos" name="cantidad_tecnicos" class="form-control" value="{{ old('cantidad_tecnicos', 1) }}" min="1" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-end">
                <button type="reset" class="btn btn-default mr-2">
                    <i class="fas fa-undo mr-1"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="fas fa-calculator mr-1"></i> Calcular y Guardar Viáticos
                </button>
            </div>
        </div>
    </form>
</div>
@endsection