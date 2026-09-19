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
    {{-- CISCO --}}
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <h4><b>SW-A ------------------------------------- CISCO, LAN , DATOS</b></h4>
                    <button class="btn btn-warning">revisado</button>
                </h3>

                <div class="card-tools">
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">


                <div class="row">
                    <div class="col-md-11">
                        <div class="row">
                            <div id="checkbox-container">
                                @for ($i = 1; $i <= 24; $i++)
                                    @php $valor = 'SW-A-' . $i; @endphp
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
                    <div class="col-md-1">
                        <div class="row">
                            <div id="checkbox-container">
                                @for ($i = 49; $i <= 51; $i += 2)
                                    @php $valor = 'SW-A-' . $i; @endphp
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
                <div class="row">
                    <div class="col-md-11">
                        <div id="checkbox-container">
                            @for ($i = 25; $i <= 48; $i++)
                                @php $valor = 'SW-A-' . $i; @endphp
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
                    <div class="col-md-1">
                        <div class="row">
                            <div id="checkbox-container">
                                @for ($i = 50; $i <= 52; $i += 2)
                                    @php $valor = 'SW-A-' . $i; @endphp
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

        </div>
    </div>



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
    {{-- SWITCH B TELEFONIA--}}
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
