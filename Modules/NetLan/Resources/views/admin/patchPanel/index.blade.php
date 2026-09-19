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

   ///////////////////////////////////
   <div class="modal fade" id="crearPatch_panelModal" tabindex="-1" role="dialog" aria-labelledby="crearPatch_panelModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ URL('netlan/admin/patchPanel/store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="crearPatch_panelModalLabel">Agregar Nuevo patch_panel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="col-md-12">
                        {{-- localidad  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Localidad</label>
                                <select name="location" id="select_emp" class="form-control select2"
                                    style="width: 100%">
                                    <option value="">Seleciones ...</option>
                                    <option value="Tegucigalpa">Tegucigalpa</option>
                                    <option value="SPS">SPS</option>
                                </select>
                            </div>
                        </div>
                        {{-- nombre  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Nombre del patch_panel</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        {{-- ports  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="ports">Puertos</label>
                                <input type="text" class="form-control" name="total_ports" required>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- modal edit  --}}


<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModeloModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="editModeloModalLabel"><b>Modificar el modelo</b></h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="form-group">
                        <label for="edit_location">Ciudad</label>
                        <input type="text" class="form-control" name="location" id="edit_location">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_name">Nombre del patch</label>
                        <input type="text" class="form-control" name="name" id="edit_name">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_total_ports">Cantidad de puertos</label>
                        <input type="number" class="form-control" name="total_ports" id="edit_total_ports">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
   ///////////////////////////////

    </div>
    </div>


@stop

@section('css')
    @include('admin.patch_panels.parciales.css')
@stop

@section('js')
    @include('admin.patch_panels.parciales.js')
@stop
