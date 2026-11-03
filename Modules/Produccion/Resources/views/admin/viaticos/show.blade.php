@extends('adminlte::page')

@section('title', 'Detalle de Viático - ' . $viatico->ciudad)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">Detalle de Viáticos</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-map-marker-alt text-primary mr-1"></i> Origen: <strong>{{ $viatico->origen ?? 'No registrado' }}</strong> · Destino: <strong>{{ $viatico->ciudad }}</strong>
            </p>
        </div>
        <div>
            <a href="{{ route('admin.viaticos.pdf', $viatico->id) }}" class="btn btn-danger shadow-sm" target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
            </a>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary shadow-sm ml-1">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    {{-- Tarjetas de resumen métrico --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Técnicos</span>
                    <span class="info-box-number">{{ $viatico->cantidad_tecnicos }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-calendar-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Días de Viaje</span>
                    <span class="info-box-number">{{ $viatico->dias }} {{ Str::plural('día', $viatico->dias) }}</span>
                </div>
            </div>
        </div>

        @if(!empty($viatico->distancia_km))
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-route"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Distancia de ida</span>
                    <span class="info-box-number">{{ number_format($viatico->distancia_km, 2) }} km</span>
                </div>
            </div>
        </div>
        @endif

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm bg-success">
                <span class="info-box-icon bg-white text-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total a pagar</span>
                    <span class="info-box-number text-lg">L{{ number_format($viatico->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Desglose de Rubros --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-receipt mr-1"></i> Desglose de Gastos
                    </h3>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Concepto / Rubro</th>
                                    <th class="text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mano de obra (monto total)</td>
                                    <td class="text-right font-weight-bold">L{{ number_format($viatico->mano_obra ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-bed text-muted mr-2"></i> Hospedaje</td>
                                    <td class="text-right font-weight-bold">L{{ number_format($viatico->hospedaje, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-utensils text-muted mr-2"></i> Alimentación</td>
                                    <td class="text-right font-weight-bold">L{{ number_format($viatico->alimentacion, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-road text-muted mr-2"></i> Peajes</td>
                                    <td class="text-right font-weight-bold">L{{ number_format($viatico->peajes, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-gas-pump text-muted mr-2"></i> Combustible</td>
                                    <td class="text-right font-weight-bold">L{{ number_format($viatico->combustible, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-plus-circle text-muted mr-2"></i> Gastos Extras</td>
                                    <td class="text-right font-weight-bold">L{{ number_format($viatico->extra, 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th class="text-right">Subtotal (viáticos + mano de obra):</th>
                                    <th class="text-right">L{{ number_format($viatico->subtotal ?? $viatico->total, 2) }}</th>
                                </tr>
                                <tr>
                                    <th class="text-right">@if($viatico->impuesto_porcentaje !== null)Impuesto ({{ number_format($viatico->impuesto_porcentaje, 0) }}%):@elseImpuesto no registrado (viático anterior):@endif</th>
                                    <th class="text-right">@if($viatico->impuesto !== null)L{{ number_format($viatico->impuesto, 2) }}@else—@endif</th>
                                </tr>
                                <tr>
                                    <th class="text-right">Total a pagar:</th>
                                    <th class="text-right text-success text-lg">L{{ number_format($viatico->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer clearfix">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Montos expresados en moneda local.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
