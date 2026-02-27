@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
    <hr>
@stop

@section('content')
    <div class="row">



        {{-- roles --}}
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box zoomp">
                <img src="{{ url('/img/roles.gif') }}" width="70px" alt="">
                <div class="info-box-content">
                    <span class="info-box-text"><b>Roles registrados</b></span>
                    <span class="info-box-number">{{ $total_roles }} roles</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        {{-- administrativos --}}
        <div class="col-md-3 col-sm-6 col-12">

            <div class="info-box zoomp">
                <img src="{{ url('/img/administrativo.gif') }}" width="70px" alt="">
                <div class="info-box-content">
                    <span class="info-box-text"><b>administrativos registrados</b></span>
                    <span class="info-box-number">{{ $total_administrativo }} administrativos</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        {{-- agenda --}}

        <div class="col-md-3 col-sm-6 col-12">
            <a href="{{ url('admin/agenda') }}">
                <div class="info-box zoomp">
                    <img src="{{ url('/img/calendario.gif') }}" width="70px" alt="">
                    <div class="info-box-content">
                        <span class="info-box-text"><b>Agenda</b></span>
                        <span class="info-box-number">{{ $total_administrativo }} </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </a>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <a href="{{ url('admin/marcaciones') }}">
                <div class="info-box zoomp">
                    <img src="{{ url('/img/aistencia.gif') }}" width="70px" alt="">
                    <div class="info-box-content">
                        <span class="info-box-text"><b>Asistencia</b></span>
                        <span class="info-box-number">{{ $total_administrativo }} </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </a>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <a href="{{ url('admin/inventarioBTR') }}">
                <div class="info-box zoomp">
                    <img src="{{ url('/img/btr.gif') }}" width="70px" alt="">
                    <div class="info-box-content">
                        <span class="info-box-text"><b>BTR</b></span>
                        {{-- <span class="info-box-number">{{ $total_administrativo }} </span> --}}
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </a>
        </div>
    </div>
    {{-- docentes --}}

  
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
