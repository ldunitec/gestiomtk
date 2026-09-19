@extends('adminlte::page')

@section('content_header')

    <h1><b>Listado de equipos </b></h1>
    <hr>
@stop

@section('content')



    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Equipos Registrados</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#marcaModal">
                            Nueva marca
                        </button>
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm " data-toggle="modal"
                            data-target="#createModal">
                            Nuevo modelo
                        </button>
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm " data-toggle="modal"
                            data-target="#crearEquipoModal">
                            Nuevo equipo
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="tabla" class="table table-bordered table-hover table-striped table-sm">
                            <thead>
                                <tr>
                                    {{-- <th style="text-align: center">Nro</th> --}}
                                    <th style="text-align: center">Nombre</th>
                                    <th style="text-align: center">Ip_Address</th>
                                    <th style="text-align: center">Tipo</th>
                                    <th style="text-align: center">marca</th>
                                    <th style="text-align: center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>

    </div>

   
    @endsection







    @section('css')

        @include('admin/equipos/parciales/css')

    @section('js')

        <script>  $(document).ready(function() {
            $('#tabla').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.equipos.list') }}',
                columns: [
                    // {data:'DT_RowIndex'},           // Nro
                    {
                        data: 'nombre'
                    }, // Nombre
                    {
                        data: 'ip_address'
                    }, // Ip_Address
                    {
                        data: 'tipo'
                    }, // Tipo
                    {
                        data: 'marca',
                        defaultContent: ''
                    }, // Marca (puedes agregar relación)
                    {
                        data: 'acciones',
                        orderable: false,
                        searchable: false
                    } // Botones
                ]
            });
            });

        </script>
      

    @stop
