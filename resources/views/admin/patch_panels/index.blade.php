@extends('adminlte::page')

@section('content_header')

    <h1><b>Listado de patch_panels </b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Patch Panel registrados</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearPatch_panelModal">
                            Nuev0
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
                                @foreach ($patch_panels as $patch_panel)
                                    <tr>
                                        <td style="text-align: center">{{ $contador++ }}</td>
                                        <td>{{ $patch_panel->location }}</td>
                                        <td>{{ $patch_panel->name }}</td>
                                        <td>{{ $patch_panel->total_ports }}</td>
                                        <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <button class="btn btn-success btn-sm" data-toggle="modal"
                                                    data-target="#editModal" data-id="{{ $patch_panel->id }}"
                                                    data-name="{{ $patch_panel->name }}"
                                                    data-total_ports="{{ $patch_panel->total_ports }}"
                                                    data-location="{{ $patch_panel->location }}">
                                                    <i class="fas fa-pencil-alt"></i> Editar

                                                </button>
                                                <form action="{{ url('/admin/patch_panels', $patch_panel->id) }}"
                                                    method="post" onclick="preguntar{{ $patch_panel->id }}(event)"
                                                    id="miFormulario{{ $patch_panel->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>

                                            </div>

                                        </td>
                                @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>

    @include('admin.patch_panels.parciales.modal')

    </div>
    </div>


@stop

@section('css')
    @include('admin.patch_panels.parciales.css')
@stop

@section('js')
    @include('admin.patch_panels.parciales.js')
@stop
