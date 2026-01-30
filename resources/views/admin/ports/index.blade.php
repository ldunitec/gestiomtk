@extends('adminlte::page')

@section('content_header')
    <h2 style="color: green; background-color: yellow;"><b>Completado</b></h2>
    <h1><b>Listado de ports </b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Puertos registrados</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">
                            Nuevo
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-hover table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Nro</th>
                                    <th style="text-align: center">Nombre</th>
                                    <th style="text-align: center">No. Puerto</th>
                                    <th style="text-align: center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach ($ports as $port)
                                    <tr>
                                        <td style="text-align: center">{{ $contador++ }}</td>
                                        <td>{{ $port->patch_panel->name }}</td>
                                        <td>{{ $port->port_number }}</td>
                                        <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <button class="btn btn-success btn-sm" data-toggle="modal"
                                                    data-target="#editModal" data-id="{{ $port->id }}"
                                                    data-patch_panel_id="{{ $port->patch_panel->id }}"
                                                    data-port_number="{{ $port->port_number }}">
                                                    <i class="fas fa-pencil-alt"></i> Editar
                                                </button>
                                                <form action="{{ url('/admin/ports', $port->id) }}" method="post"
                                                    onclick="preguntar{{ $port->id }}(event)"
                                                    id="miFormulario{{ $port->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    </div>

    @include('admin.ports.parciales.modal');

    </div>
    </div>


@stop

@section('css')
    @include('admin.ports.parciales.css')
@stop

@section('js')
    @include('admin.ports.parciales.js')
@stop
