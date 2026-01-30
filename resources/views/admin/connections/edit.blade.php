@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de conexiones </b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Conexiones registradas</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearPortsModal">
                            Nueva
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ url('/admin/connections', $conexion->id) }}" method="post">
                        @csrf
                        @method('PUT')

                        <div class="col-md-12">

                            {{-- nombre  --}}
                            <div class="col-md-12">
                                <div>
                                    <label for="selectnomb">Nombre del equipo</label>
                                    <select id="selectnomb" name="equipos_id"
                                        class="form-control @error('equipos_id') is-invalid @enderror" style="width: 100%">
                                        <option value="">Seleccione ...</option>
                                        @foreach ($equipos as $equipo)
                                            <option value="{{ $equipo->id }}"
                                                {{ old('equipos_id', $conexion->equipos_id ?? '') == $equipo->id ? 'selected' : '' }}>
                                                {{ $equipo->nombre }} / {{ $equipo->ip_address }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('equipos_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- puerto  --}}
                            <div class="col-md-12">
                                <div>
                                    <label for="nombre">Puerto Patch panel</label>
                                    <select name="port_id" id="selectPatch" class="form-control select2"
                                        style="width: 100%">
                                        <option value="">Seleciones ...</option>
                                        @foreach ($ports as $port)
                                            <option value="{{ $port->id }}"
                                                {{ old('equipos_id', $conexion->port_id ?? '') == $port->id ? 'selected' : '' }}
                                                data-nombrepatch="{{ $port->patch_panel->name }}-{{ $port->port_number }}">
                                                {{ $port->patch_panel->name }}-{{ $port->port_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- SWITCH --}}
                            <div class="col-md-12">
                                <div>
                                    <label for="nombre">Puerto Switch</label>
                                    <select name="" id="selectsw" class="form-control select2" style="width: 100%">
                                        <option value="Seleccion switch"></option>
                                        @foreach ($swPorts as $swRout)
                                            <option value="{{ $swRout->id }}" 
                                                {{  ($conexion->port_id ?? '') == $swRout->id ? 'selected' : '' }} 
                                             data-nombresw="{{ $swRout->sw_mk->name }}-{{ $swRout->sw_port_number }}">
                                            {{ $swRout->sw_mk->name }}-{{ $swRout->sw_port_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                             {{-- estado --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Estado</label>
                                <select name="estado" id="" class="form-control selectsw select2"
                                    style="width: 100%">
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                        
                                </select>
                            </div>
                        </div>

                             {{-- SWITCH --}}
                            <div class="col-md-12">
                                <div>
                                    <label for="nombre">Comentario</label>
                                     <input type="text" class="form-control" name="comentario" value="{{ $conexion->comentario }}" >
                                </div>
                            </div>

<hr>

                            <div class="col-md-12">
                                <input type="text" id="nombrepatch" value="{{ $portName }}" " name="nombre">

                            </div>

<hr>
                            <div class="col-md-12">
                                <input type="text" id="nombresw" value="{{ $swSW }}" name="sw">

                            </div>


                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <a href="{{ url('/admin/connections') }}" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-success">Actualizar</button>
                                </div>
                            </div>
                        </div>


                    </form>

                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>

    @include('admin.connections.parciales.modal')

    </div>
    </div>


@stop

@section('css')
    @include('admin.connections.parciales.css')
@stop

@section('js')

    @include('admin.connections.parciales.js')

@stop
