@extends('adminlte::page')

@section('content_header')

    <h1><b>Listado de switch y Router </b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Switch y router registrados</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearsw_mikModal">
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
                                    <th style="text-align: center">Localidad</th>
                                    <th style="text-align: center">Nombre</th>
                                    <th style="text-align: center">Puertos</th>
                                    <th style="text-align: center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach ($switchs as $sw_mik)
                                    <tr>
                                        <td style="text-align: center">{{ $contador++ }}</td>
                                        <td>{{ $sw_mik->location }}</td>
                                        <td>{{ $sw_mik->name }}</td>
                                        <td>{{ $sw_mik->total_ports }}</td>
                                        <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <button class="btn btn-success btn-sm" data-toggle="modal"
                                                    data-target="#editModal" data-id="{{ $sw_mik->id }}"
                                                    data-name="{{ $sw_mik->name }}"
                                                    data-total_ports="{{ $sw_mik->total_ports }}"
                                                    data-location="{{ $sw_mik->location }}">
                                                    <i class="fas fa-pencil-alt"></i> Editar

                                                </button>
                                                <form action="{{ url('/admin/switchs', $sw_mik->id) }}" method="post"
                                                    onclick="preguntar{{ $sw_mik->id }}(event)"
                                                    id="miFormulario{{ $sw_mik->id }}">
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

    @include('admin.switchs.parciales.modal');

    </div>
    </div>


@stop

@section('css')
    @include('admin.switchs.parciales.css')
@stop

@section('js')
    @include('admin.switchs.parciales.js')
@stop
