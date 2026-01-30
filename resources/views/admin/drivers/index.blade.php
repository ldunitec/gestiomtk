@extends('adminlte::page')

@section('title', 'Listado Drivers')

@section('content_header')
    <h1>Drivers y Manuales</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">

            <div class="row">
                <a href="{{ url('admin/evolis') }}" class="small-box-footer">
                    <div class="col-md-3 mb-2">
                        <!-- small card -->
                        <div class="small-box bg-white">
                            <div class="inner">
                                <h3>Evolis</h3>

                                <p>Driver, Manuales,Firmware y Herramientas</p>
                            </div>
                            <div class="icon">
                                <img src="https://es.evolis.com/wp-content/uploads/2021/12/logobaseline-evolis-rgb.svg"
                                    alt="" width="120px">
                            </div>
                            <a href="{{ url('admin/evolis') }}" class="small-box-footer">
                                Mas Informacion <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </a>
                <!-- ./col -->
                <a href="{{ url('admin/zebra') }}" class="small-box-footer">
                    <div class="col-md-3 mb-2">
                        <!-- small card -->
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>ZEBRA<sup style="font-size: 20px"></sup></h3>
                                <p>Driver, Manuales,Firmware y Herramientas</p>
                            </div>
                            <div class="icon">
                                <img src="https://www.zebra.com/content/experience-fragments/zebra1/us/en/header/master/_jcr_content/root/image.coreimg.svg/1757099569592/zebra-logo-horizontal.svg"
                                    alt="">

                            </div>
                            <a href="#" class="small-box-footer">
                                Mas Informacion <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </a>
                <!-- ./col -->
                <a href="{{ url('admin/morak') }}" class="small-box-footer">

                    <div class="col-md-3 mb-2">
                        <!-- small card -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>MORAK<sup style="font-size: 20px"></sup></h3>
                                <p>Driver, Manuales,Firmware y Herramientas</p>
                            </div>
                            <div class="icon">
                                <img src="https://www.moraksystem.com/web/image/website/4/logo/Morak%20system?unique=0bef3dc"
                                    alt="" width="93px">

                            </div>
                            <a href="{{ url('admin/morak') }}" class="small-box-footer">
                                Mas Informacion <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </a>
                <a href="{{ url('admin/cardpresso') }}" class="small-box-footer">
                    <div class="col-md-3 mb-2">
                        <!-- small card -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>CARDPRESSO<sup style="font-size: 20px"></sup></h3>
                                <p>Software, Manuales y Herramientas</p>
                            </div>
                            <div class="icon">
                                <hr><hr>
                                <img src="https://www.cardpresso.com/wp-content/uploads/2024/01/cardpresso_logo_white.png"
                                    alt="" width="150px">

                            </div>
                            <a href="{{ url('admin/cardpresso') }}" class="small-box-footer">
                                Mas Informacion <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </a>

            </div>
        </div>

    </div>

@stop

@section('js')
    <script>
        $(function() {
            $('#drivers').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                }
            });
        });
    </script>
@stop
