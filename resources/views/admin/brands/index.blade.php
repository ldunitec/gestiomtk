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
                    <img src="{{ $brand->logo_url }}"
                         alt="{{ $brand->name }}"
                         style="max-width:120px;">
                </div>

                <div class="small-box-footer">
                    Más Información <i class="fas fa-arrow-circle-right"></i>
                </div>
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
            height: 180px;
            /* altura fija */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .small-box .inner {
            flex: 1;
        }

        .small-box .icon {
            bottom: 10px;
            top: auto;
        }

        .small-box img {
            max-height: 80px;
            object-fit: contain;
        }
    </style>

@stop
