@extends('adminlte::page')

@section('title', 'Listado de Marcas')

@section('content_header')
    <center>
        <h1>LISTADO DE MARCAS</h1>
    </center>
    <div class="card-tools">
        <!-- Botón para abrir el modal -->
        <button type="button" class="btn btn-primary btn-sm" h-50 data-toggle="modal" data-target="#modalFile">
            Nueva Archivo
        </button>
        <button type="button" class="btn btn-warning btn-sm" h- 50 data-toggle="modal" data-target="#modalBrand">
            Nueva marca
        </button>
    </div>
@stop

@section('content')
    <div class="row">
    @foreach ($brands as $brand)
    <div class="col-md-3 mb-2">
        <a href="{{ route('admin.brands.show', $brand->id) }}">
            <div class="small-box bg-{{ $brand->color_primary }} position-relative">

                {{-- RIBBON SI NO HAY ARCHIVOS --}}
                @if ($brand->files_count == 0)
                    <div class="ribbon-wrapper ribbon-lg">
                        <div class="ribbon bg-danger text-lg">
                            Sin archivos
                        </div>
                    </div>
                @endif

                <div class="inner">
                    <h3>{{ strtoupper($brand->name) }}</h3>
                    <p>Driver, Manuales, Firmware y Herramientas</p>
                </div>

                <div class="icon">
                    {{-- <img src="{{ $brand->logo_url }}"
                         alt="{{ $brand->name }}"
                         style="max-width:120px;"> --}}
                         <img src="{{ asset($brand->logo_url) }}" width="100">
                </div>

                {{-- <div class="small-box-footer">
                    Más Información <i class="fas fa-arrow-circle-right"></i>
                </div> --}}
            </div>
        </a>
    </div>
@endforeach



    </div>
    @include('admin.parciales.modal')
@stop

@section('css')
    <style>
       .small-box {
    height: 180px;              /* altura fija */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;           /* evita que el contenido agrande el card */
}

.small-box .inner {
    flex: 1;
    overflow: hidden;
}

.small-box h3 {
    font-size: 1.1rem;
    white-space: nowrap;        /* evita salto de línea */
    overflow: hidden;
    text-overflow: ellipsis;    /* ... si el texto es largo */
}

.small-box p {
    font-size: 0.85rem;
    margin-bottom: 0;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;      /* máximo 2 líneas */
    -webkit-box-orient: vertical;
}

.small-box .icon {
    position: absolute;
    bottom: 10px;
    right: 10px;
}

.small-box img {
    max-height: 80px;
    max-width: 100%;
    object-fit: contain;
}

.ribbon-wrapper {
    z-index: 10;
}
    </style>

@stop
