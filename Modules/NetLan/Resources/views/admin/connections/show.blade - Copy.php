@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de conexiones </b></h1>
    <hr>
@stop

@section('content')

    {{-- MIKROTIK TEST --}}
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h4><b>MIKROTIK TEST------------------------------------- VLAN'S, LAN</b></h4>
                <button class="btn btn-warning">Revisado</button>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="checkbox-container">
                            @for ($i = 1; $i <= 13; $i++)
                                @php
                                    $valor = 'MKRT-01-' . $i;
                                @endphp

                                <label style="margin-right: 0px;" class="custom-label">
                                    @if (in_array($valor, $switches))
                                        <button type="button" class="btn btn-success btn-sm btnPort" {{-- data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="{{ $valor }}" --}}
                                            data-valor="{{ $valor }}">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-secondary btn-sm">{{ $i }}</button>
                                    @endif

                                </label>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





    {{-- MIKROTIK --}}
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>MIKROTIK ------------------------------------- VLAN'S, LAN</b></h4>
                    <button class="btn btn-warning">revisado</button>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">

                        <div id="checkbox-container">
                            @for ($i = 1; $i <= 13; $i++)
                                @php $valor = 'MKRT-01-' . $i; @endphp
                                <label style="margin-right: 0px; " class="custom-label">
                                    @if (in_array($valor, $switches))
                                        <button type="button" class="btn btn-success btn-sm btnPort"
                                            data-valor="{{ $valor }}">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-secondary btn-sm">{{ $i }}</button>
                                    @endif
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    {{-- PP-A  --}}
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>Patch Panel A ------------------------------------- Datos</b></h4>
                    <button class="btn btn-warning">revisado</button>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">

                        <div id="checkbox-container">
                            @for ($i = 1; $i <= 24; $i++)
                                @php $valor = 'PP-A-' . $i; @endphp
                                <label style="margin-right: 0px; " class="custom-label">
                                    @if (in_array($valor, $puerto))
                                        <button type="button" class="btn btn-success btn-m btnPort"
                                            data-valor="{{ $valor }}">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-secondary btn-sm">{{ $i }}</button>
                                    @endif
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
 
  
{{-- CISCO SG300-52 --}}
<div class="col-md-12">
    @php
        $conexionesCisco = $conexiones
            ->filter(function ($conexion) {
                return !empty($conexion->sw)
                    && str_starts_with($conexion->sw, 'SW-A-');
            })
            ->groupBy('sw');

        $estadoPuertoCisco = function ($registros) {
            $cantidad = $registros->count();

            if ($cantidad === 0) {
                return 'libre';
            }

            if ($cantidad > 1) {
                return 'multiple';
            }

            $equipo = $registros->first()->equipo;

            if ($equipo && $equipo->estado === 'Activo') {
                return 'activo';
            }

            return 'inactivo';
        };

        $clasePuertoCisco = function ($estado) {
            return match ($estado) {
                'activo'   => 'activo',
                'inactivo' => 'inactivo',
                'multiple' => 'multiple',
                default    => 'libre',
            };
        };
    @endphp

    <div class="card cisco-card">

        {{-- HEADER --}}
        <div class="card-header cisco-header">

            <div>
                <h3 class="cisco-title mb-0">
                    <i class="fas fa-network-wired mr-2"></i>
                    CISCO SG300-52
                </h3>

                <small class="cisco-subtitle">
                    Mapa de Puertos — MEGATK
                </small>
            </div>

            <div class="cisco-status">
                <span class="cisco-led led-green"></span>
                SWITCH
            </div>

        </div>


        {{-- BODY --}}
        <div class="card-body cisco-body">

            {{-- INFORMACIÓN --}}
            <div class="cisco-info mb-3">

                <div>
                    <strong>SW-A</strong>
                    <span class="text-muted ml-2">
                        Cisco Small Business SG300-52
                    </span>
                </div>

                <div class="cisco-port-count">
                    52 PUERTOS
                </div>

            </div>


            {{-- PUERTOS --}}
            <div class="cisco-panel">

                {{-- PUERTOS 1 - 24 --}}
                <div class="cisco-row">

                    @for ($i = 1; $i <= 24; $i++)

                        @php
                            $valor = 'SW-A-' . $i;

                            $registros = $conexionesCisco
                                ->get($valor, collect());

                            $cantidad = $registros->count();

                            $estado = $estadoPuertoCisco($registros);

                            $clase = $clasePuertoCisco($estado);
                        @endphp

                        <button
                            type="button"
                            class="cisco-port btnPort {{ $clase }}"
                            data-valor="{{ $valor }}"
                            data-toggle="tooltip"
                            data-placement="top"
                            title="{{ $valor }} — {{ ucfirst($estado) }}"
                        >

                            <span class="port-number">
                                {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <span class="port-jack">
                                <span class="port-led"></span>
                            </span>

                            @if ($cantidad > 1)
                                <span class="port-badge">
                                    {{ $cantidad }}
                                </span>
                            @endif

                        </button>

                    @endfor

                </div>


                {{-- SEPARADOR --}}
                <div class="cisco-separator">
                    <span>10/100/1000 Mbps</span>
                </div>


                {{-- PUERTOS 25 - 48 --}}
                <div class="cisco-row">

                    @for ($i = 25; $i <= 48; $i++)

                        @php
                            $valor = 'SW-A-' . $i;

                            $registros = $conexionesCisco
                                ->get($valor, collect());

                            $cantidad = $registros->count();

                            $estado = $estadoPuertoCisco($registros);

                            $clase = $clasePuertoCisco($estado);
                        @endphp

                        <button
                            type="button"
                            class="cisco-port btnPort {{ $clase }}"
                            data-valor="{{ $valor }}"
                            data-toggle="tooltip"
                            data-placement="top"
                            title="{{ $valor }} — {{ ucfirst($estado) }}"
                        >

                            <span class="port-number">
                                {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <span class="port-jack">
                                <span class="port-led"></span>
                            </span>

                            @if ($cantidad > 1)
                                <span class="port-badge">
                                    {{ $cantidad }}
                                </span>
                            @endif

                        </button>

                    @endfor

                </div>


                {{-- UPLINKS --}}
                <div class="cisco-uplinks">

                    <div class="uplink-label">
                        UPLINK
                    </div>


                    @foreach ([49, 50, 51, 52] as $i)

                        @php
                            $valor = 'SW-A-' . $i;

                            $registros = $conexionesCisco
                                ->get($valor, collect());

                            $cantidad = $registros->count();

                            $estado = $estadoPuertoCisco($registros);

                            $clase = $clasePuertoCisco($estado);
                        @endphp


                        <button
                            type="button"
                            class="cisco-port uplink-port btnPort {{ $clase }}"
                            data-valor="{{ $valor }}"
                            data-toggle="tooltip"
                            data-placement="top"
                            title="{{ $valor }} — {{ ucfirst($estado) }}"
                        >

                            <span class="port-number">
                                {{ $i }}
                            </span>

                            <span class="port-jack">
                                <span class="port-led"></span>
                            </span>

                            @if ($cantidad > 1)
                                <span class="port-badge">
                                    {{ $cantidad }}
                                </span>
                            @endif

                        </button>

                    @endforeach

                </div>

            </div>


            {{-- LEYENDA --}}
            <div class="cisco-legend mt-4">

                <div class="legend-item">
                    <span class="legend-led libre"></span>
                    Libre
                </div>

                <div class="legend-item">
                    <span class="legend-led activo"></span>
                    Activo
                </div>

                <div class="legend-item">
                    <span class="legend-led inactivo"></span>
                    Inactivo
                </div>

                <div class="legend-item">
                    <span class="legend-led multiple"></span>
                    Múltiple
                </div>

            </div>

        </div>

    </div>
</div>


{{-- ESTILOS CISCO --}}
<style>

    .cisco-card {
        border-radius: 8px;
        overflow: hidden;
        background: #1f252b;
        border: 1px solid #343a40;
        box-shadow: 0 4px 15px rgba(0,0,0,.25);
    }

    .cisco-header {
        background: linear-gradient(
            180deg,
            #343a40 0%,
            #212529 100%
        );
        color: #fff;

        display: flex;
        justify-content: space-between;
        align-items: center;

        padding: 14px 20px;
    }

    .cisco-title {
        font-size: 20px;
        letter-spacing: .5px;
    }

    .cisco-subtitle {
        color: #adb5bd;
    }

    .cisco-status {
        font-size: 12px;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .cisco-led {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        margin-right: 6px;

        box-shadow: 0 0 7px rgba(255,255,255,.5);
    }

    .led-green {
        background: #28a745;
    }


    .cisco-body {
        background: #252b31;
        padding: 20px;
    }


    .cisco-info {
        display: flex;
        justify-content: space-between;
        align-items: center;

        color: #f8f9fa;
        font-size: 14px;
    }

    .cisco-port-count {
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 4px;
        background: #343a40;
        color: #ced4da;
    }


    .cisco-panel {
        background: #161a1d;

        border: 1px solid #495057;
        border-radius: 6px;

        padding: 18px;

        box-shadow:
            inset 0 0 20px rgba(0,0,0,.7),
            0 3px 8px rgba(0,0,0,.3);
    }


    .cisco-row {
        display: grid;

        grid-template-columns:
            repeat(24, minmax(28px, 1fr));

        gap: 7px;
    }


    .cisco-port {
        position: relative;

        height: 58px;

        padding: 4px;

        border: 1px solid #495057 !important;

        border-radius: 4px;

        background: #343a40;

        color: #fff;

        cursor: pointer;

        transition:
            transform .12s ease,
            box-shadow .12s ease,
            border-color .12s ease;
    }


    .cisco-port:hover {
        transform: translateY(-2px);

        border-color: #fff !important;

        box-shadow:
            0 0 8px rgba(255,255,255,.25);
    }


    .port-number {
        display: block;

        font-size: 10px;

        font-weight: bold;

        line-height: 12px;

        margin-bottom: 4px;
    }


    .port-jack {
        display: flex;

        align-items: center;
        justify-content: center;

        height: 29px;

        background: #111;

        border: 1px solid #000;

        border-radius: 3px;

        box-shadow:
            inset 0 2px 4px rgba(255,255,255,.05);
    }


    .port-led {
        width: 7px;
        height: 7px;

        border-radius: 50%;

        background: #555;

        box-shadow:
            0 0 4px rgba(255,255,255,.2);
    }


    /* ESTADOS */

    .cisco-port.libre {
        background: #343a40;
    }

    .cisco-port.libre .port-led {
        background: #6c757d;
    }


    .cisco-port.activo {
        background: #176b35;
        border-color: #28a745 !important;
    }

    .cisco-port.activo .port-led {
        background: #28a745;

        box-shadow:
            0 0 7px #28a745;
    }


    .cisco-port.inactivo {
        background: #856404;
        border-color: #ffc107 !important;
    }

    .cisco-port.inactivo .port-led {
        background: #ffc107;

        box-shadow:
            0 0 7px #ffc107;
    }


    .cisco-port.multiple {
        background: #842029;
        border-color: #dc3545 !important;
    }

    .cisco-port.multiple .port-led {
        background: #dc3545;

        box-shadow:
            0 0 8px #dc3545;
    }


    .port-badge {
        position: absolute;

        top: 2px;
        right: 3px;

        min-width: 16px;

        padding: 1px 4px;

        border-radius: 8px;

        background: #fff;
        color: #212529;

        font-size: 9px;

        font-weight: bold;
    }


    .cisco-separator {
        margin: 15px 0;

        border-top: 1px solid #343a40;

        text-align: center;

        position: relative;
    }

    .cisco-separator span {
        position: relative;

        top: -9px;

        background: #161a1d;

        padding: 0 12px;

        color: #6c757d;

        font-size: 9px;

        letter-spacing: 1px;
    }


    /* UPLINKS */

    .cisco-uplinks {
        display: flex;

        justify-content: flex-end;

        align-items: center;

        gap: 8px;

        margin-top: 18px;

        padding-top: 15px;

        border-top: 1px solid #343a40;
    }

    .uplink-label {
        color: #adb5bd;

        font-size: 10px;

        font-weight: bold;

        letter-spacing: 1px;

        margin-right: 5px;
    }

    .uplink-port {
        width: 55px;

        flex: 0 0 55px;
    }


    /* LEYENDA */

    .cisco-legend {
        display: flex;

        justify-content: center;

        flex-wrap: wrap;

        gap: 20px;

        color: #ced4da;

        font-size: 12px;
    }

    .legend-item {
        display: flex;

        align-items: center;
    }

    .legend-led {
        width: 10px;
        height: 10px;

        border-radius: 50%;

        margin-right: 6px;
    }

    .legend-led.libre {
        background: #6c757d;
    }

    .legend-led.activo {
        background: #28a745;
        box-shadow: 0 0 5px #28a745;
    }

    .legend-led.inactivo {
        background: #ffc107;
        box-shadow: 0 0 5px #ffc107;
    }

    .legend-led.multiple {
        background: #dc3545;
        box-shadow: 0 0 5px #dc3545;
    }


    /* RESPONSIVE */

    @media (max-width: 1200px) {

        .cisco-row {
            grid-template-columns:
                repeat(12, minmax(35px, 1fr));
        }

    }


    @media (max-width: 768px) {

        .cisco-row {
            grid-template-columns:
                repeat(6, 1fr);
        }

        .cisco-info {
            flex-direction: column;
            align-items: flex-start;

            gap: 8px;
        }

    }

</style>

    {{-- PP-B  --}}
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>Patch Panel B ------------------------------------- Datos</b></h4>
                    <button class="btn btn-warning">revisado</button>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">

                        <div id="checkbox-container">
                            @for ($i = 1; $i <= 24; $i++)
                                @php $valor = 'PP-B-' . $i; @endphp
                                <label style="margin-right: 0px; " class="custom-label">
                                    @if (in_array($valor, $puerto))
                                        <button type="button" class="btn btn-success btn-sm btnPort"
                                            data-valor="{{ $valor }}">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button type="button"
                                            class="btn btn-secondary btn-sm">{{ $i }}</button>
                                    @endif
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>

    {{-- PP-C  --}}
    <div class="col-md-12">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>Patch Panel C ------------------------------------- Telefonia</b></h4>
                    <button class="btn btn-warning">revisado</button>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">

                        <div id="checkbox-container">
                            @for ($i = 1; $i <= 24; $i++)
                                @php $valor = 'PP-C-' . $i; @endphp
                                <label style="margin-right: 0px; " class="custom-label">
                                    @if (in_array($valor, $puerto))
                                        <button type="button" class="btn btn-success btn-sm btnPort"
                                            data-valor="{{ $valor }}">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button type="button"
                                            class="btn btn-secondary btn-sm">{{ $i }}</button>
                                    @endif
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    {{-- SWITCH B TELEFONIA --}}
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>SWITCH B ------------------------------------- Telefonia</b></h4>
                    <button class="btn btn-danger">revisado</button>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">

                        <div id="checkbox-container">
                            @for ($i = 1; $i <= 24; $i++)
                                @php $valor = 'SW-B-' . $i; @endphp
                                <label style="margin-right: 0px; " class="custom-label">
                                    @if (in_array($valor, $switches))
                                        <button type="button" class="btn btn-success btn-sm btnPort"
                                            data-valor="{{ $valor }}">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button type="button"
                                            class="btn btn-secondary btn-sm">{{ $i }}</button>
                                    @endif
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>

    {{-- PP-D  --}}
    <div class="col-md-12">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>Patch Panel D ------------------------------------- Telefonia</b></h4>
                    <button class="btn btn-warning">revisado</button>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">

                        <div id="checkbox-container">
                            @for ($i = 1; $i <= 24; $i++)
                                @php $valor = 'PP-D-' . $i; @endphp
                                <label style="margin-right: 0px; " class="custom-label">
                                    @if (in_array($valor, $puerto))
                                        <button type="button" class="btn btn-success btn-sm btnPort"
                                            data-valor="{{ $valor }}">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button type="button"
                                            class="btn btn-secondary btn-sm">{{ $i }}</button>
                                    @endif
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>

    {{-- PP-E  --}}
    <div class="col-md-12">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>Patch Panel E ------------------------------------- CCTV</b></h4>
                    <button class="btn btn-warning">revisado</button>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">

                        <div id="checkbox-container">
                            @for ($i = 1; $i <= 24; $i++)
                                @php $valor = 'PP-E-' . $i; @endphp
                                <label style="margin-right: 0px; " class="custom-label">
                                    @if (in_array($valor, $puerto))
                                        <button type="button" class="btn btn-success btn-sm btnPort"
                                            data-valor="{{ $valor }}">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button type="button"
                                            class="btn btn-secondary btn-sm">{{ $i }}</button>
                                    @endif
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    {{-- SWITCH C 48PORT --}}
    <div class="col-md-12">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>SW-C -------------------------------------CCTV, DATOS VARIOS</b></h4>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div id="checkbox-container">
                                @for ($i = 1; $i <= 24; $i++)
                                    @php $valor = 'SW-C-' . $i; @endphp
                                    <label style="margin-right: 0px; " class="custom-label">
                                        @if (in_array($valor, $switches))
                                            <button type="button" class="btn btn-success btn-sm btnPort"
                                                data-valor="{{ $valor }}">
                                                {{ $i }}
                                            </button>
                                        @else
                                            <button type="button"
                                                class="btn btn-secondary btn-sm">{{ $i }}</button>
                                        @endif
                                    </label>
                                @endfor
                            </div>
                        </div>
                        <div class="row">
                            <div id="checkbox-container">
                                @for ($i = 25; $i <= 48; $i++)
                                    @php $valor = 'SW-C-' . $i; @endphp
                                    <label style="margin-right: 0px; " class="custom-label">
                                        @if (in_array($valor, $switches))
                                            <button type="button" class="btn btn-success btn-sm btnPort"
                                                data-valor="{{ $valor }}">
                                                {{ $i }}
                                            </button>
                                        @else
                                            <button type="button"
                                                class="btn btn-secondary btn-sm">{{ $i }}</button>
                                        @endif
                                    </label>
                                @endfor
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    {{-- SWITCH D 48PORT --}}
    <div class="col-md-12">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>SW-D -------------------------------------CCTV, DATOS VARIOS</b></h4>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div id="checkbox-container">
                                @for ($i = 1; $i <= 24; $i++)
                                    @php $valor = 'SW-D-' . $i; @endphp
                                    <label style="margin-right: 0px; " class="custom-label">
                                        @if (in_array($valor, $switches))
                                            <button type="button" class="btn btn-success btn-sm btnPort"
                                                data-valor="{{ $valor }}">
                                                {{ $i }}
                                            </button>
                                        @else
                                            <button type="button"
                                                class="btn btn-secondary btn-sm">{{ $i }}</button>
                                        @endif
                                    </label>
                                @endfor
                            </div>
                        </div>


                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>

    {{-- PP-F  --}}
    <div class="col-md-12">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>Patch Panel F ------------------------------------- CCTV / Varios</b></h4>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-12">

                        <div id="checkbox-container">
                            @for ($i = 1; $i <= 24; $i++)
                                @php $valor = 'PP-F-' . $i; @endphp
                                <label style="margin-right: 0px; " class="custom-label">
                                    @if (in_array($valor, $puerto))
                                        <button type="button" class="btn btn-success btn-sm btnPort"
                                            data-valor="{{ $valor }}">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button type="button"
                                            class="btn btn-secondary btn-sm">{{ $i }}</button>
                                    @endif
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">

                        <div id="checkbox-container">
                            @for ($i = 25; $i <= 48; $i++)
                                @php $valor = 'PP-F-' . $i; @endphp
                                <label style="margin-right: 0px; " class="custom-label">
                                    @if (in_array($valor, $puerto))
                                        <button type="button" class="btn btn-success btn-sm btnPort"
                                            data-valor="{{ $valor }}">
                                            {{ $i }}
                                        </button>
                                    @else
                                        <button type="button"
                                            class="btn btn-secondary btn-sm">{{ $i }}</button>
                                    @endif
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>


    @include('netlan::admin.connections.parciales.modal');

    </div>
    </div>


@stop

@section('css')

    @include('netlan::admin.connections.parciales.css')
@stop

@section('js')

    @include('netlan::admin.connections.parciales.js')

@stop
