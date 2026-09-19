@extends('adminlte::page')

@section('content_header')

    <h1><b>Listado de equipos </b></h1>
    <hr>
@stop

@section('content')
    @include('admin.equipos.parciales.modal')


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
                        <button type="button" class="btn btn-primary btn-sm " data-toggle="modal" data-target="#createModal">
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
{{-- 
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <select id="filtroTipo" class="form-control">
                                <option value="">-- Todos los tipos --</option>
                                <option value="PC">PC</option>
                                <option value="Laptop">CAMARA</option>
                                <option value="Servidor">ROUTER</option>
                                <option value="Servidor">IMPRESORA</option>
                                <option value="Servidor">BIOMETRICO</option>
                            </select>
                        </div>
                    </div> --}}
                    <div class="table-responsive">
                        <table id="equipoActivo" class="table table-bordered table-hover table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Nro</th>
                                    <th style="text-align: center">Asignado</th>
                                    {{-- <th style="text-align: center">Departamento</th> --}}
                                    {{-- <th style="text-align: center">Nombre del equipo</th>
                                    <th style="text-align: center">Marca</th> --}}
                                    <th style="text-align: center">Ip_Address</th>
                                    <th style="text-align: center">Mac_Address</th>
                                    <th style="text-align: center">Tipo</th>
                                    <th style="text-align: center">Antivirus</th>
                                    <th style="text-align: center">Estado</th>
                                    <th style="text-align: center">Actualizado</th>
                                    <th style="text-align: center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach ($activos as $equipo)
                               
                                    <tr>
                                        <td style="text-align: center">{{ $contador++ }}</td>

                                        <td>
                                            {{ $equipo->empleado ? $equipo->empleado->nombres . ' ' . $equipo->empleado->apellidos : 'Sin empleado' }}
                                        </td>
                                
                                        <td>{{ $equipo->ip_address }}</td>
                                        <td>{{ $equipo->mac_address }}</td>
                                        <td>{{ $equipo->tipo }}</td>

                                        <td>{{ $equipo->antivirus }}</td>
                                        <td>
                                            <a href="javascript:void(0)"
                                                class="btn btn-sm toggle-estado {{ $equipo->estado == 'Activo' ? 'btn-success' : 'btn-danger' }}"
                                                data-id="{{ $equipo->id }}">
                                                {{-- <i class="fas fa-desktop"></i> - --}}
                                                {{ $equipo->estado == 'Activo' ? 'Act' : 'InAct' }}
                                            </a>
                                        </td>
                                   
                                        <td >
                                            <button disabled="disabled" class="btn btn-warning btn-sm" style="color: black "> {{ \Carbon\Carbon::parse($equipo->updated_at)->translatedFormat('F Y') }}</button>
                                        </td>


                                        <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <a href="{{ url('/admin/equipos/' . $equipo->id . '/edit') }}"
                                                    class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>


                                                <form action="{{ url('/admin/equipos', $equipo->id) }}" method="post"
                                                    onclick="preguntar{{ $equipo->id }}(event)"
                                                    id="miFormulario{{ $equipo->id }}">
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
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">Equipos Inactivos</h3>

                    <div class="card-tools">
                     
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    {{-- <div class="row mb-2">
                        <div class="col-md-3">
                            <select id="filtroTipo" class="form-control">
                                <option value="">-- Todos los tipos --</option>
                                <option value="PC">PC</option>
                                <option value="Laptop">CAMARA</option>
                                <option value="Servidor">ROUTER</option>
                                <option value="Servidor">IMPRESORA</option>
                                <option value="Servidor">BIOMETRICO</option>
                            </select>
                        </div>
                    </div> --}}
                    <div class="table-responsive">
                        <table id="equipoInactivo" class="table table-bordered table-hover table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Nro</th>
                                    <th style="text-align: center">Asignado</th>
                                    {{-- <th style="text-align: center">Departamento</th> --}}
                                    {{-- <th style="text-align: center">Nombre del equipo</th>
                                    <th style="text-align: center">Marca</th> --}}
                                    <th style="text-align: center">Ip_Address</th>
                                    <th style="text-align: center">Mac_Address</th>
                                    {{-- <th style="text-align: center">Tipo</th> --}}
                                    {{-- <th style="text-align: center">Antivirus</th> --}}
                                    <th style="text-align: center">Estado</th>
                                    <th style="text-align: center">Modificado</th>
                                    {{-- <th style="text-align: center">Acción</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach ($inActivos as $equipo)
                                    {{-- <pre>{{ dd($equipo->empleado) }}</pre> --}}
                                    {{-- {{ optional($equipo->empleado)->nombres . ' ' . optional($equipo->empleado)->apellidos ?? 'Sin empleado' }} --}}


                                    <tr>
                                        <td style="text-align: center">{{ $contador++ }}</td>

                                        <td>
                                            {{ $equipo->empleado ? $equipo->empleado->nombres . ' ' . $equipo->empleado->apellidos : 'Sin empleado' }}
                                        </td>
                                        {{-- <td>
                                            @if ($equipo->empleado && $equipo->empleado->departamento)
                                                {{ $equipo->empleado->departamento->nombre }}
                                            @else
                                                Sin Depto
                                            @endif
                                        </td> --}}

                                        {{-- <td> {{ $equipo->nombre }}</td>
                                        <td>{{ optional($equipo->marca)->nombre ?? 'Sin marca' }}</td> --}}

                                        <td>{{ $equipo->ip_address }}</td>
                                        <td>{{ $equipo->mac_address }}</td>
                                        {{-- <td>{{ $equipo->tipo }}</td> --}}

                                        {{-- <td>{{ $equipo->antivirus }}</td> --}}
                                        <td>
                                            <a href="javascript:void(0)"
                                                class="btn btn-sm toggle-estado {{ $equipo->estado == 'Activo' ? 'btn-success' : 'btn-danger' }}"
                                                data-id="{{ $equipo->id }}">
                                                {{-- <i class="fas fa-desktop"></i> - --}}
                                                {{ $equipo->estado == 'Activo' ? 'Act' : 'InAct' }}
                                            </a>
                                        </td>
                                        {{-- <td><span class="badge bg-primary">
                                                {{ \Carbon\Carbon::parse($equipo->updated_at)->translatedFormat('F Y') }}
                                            </span>
                                        </td> --}}
                                        <td >
                                            <button disabled="disabled" class="btn btn-info btn-sm" style="color: black "> {{ \Carbon\Carbon::parse($equipo->updated_at)->translatedFormat('F Y') }}</button>
                                        </td>


                                        {{-- <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <a href="{{ url('/admin/equipos/' . $equipo->id . '/edit') }}"
                                                    class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>


                                                <form action="{{ url('/admin/equipos', $equipo->id) }}" method="post"
                                                    onclick="preguntar{{ $equipo->id }}(event)"
                                                    id="miFormulario{{ $equipo->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>


                                            </div>
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>

    {{-- MODAL MODELO --}}
    <!-- Modal create -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="crearModeloModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.modelos.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="crearModeloModalLabel">Agregar Nuevo modelo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nombre">Marca</label>
                            <select name="marca_id" id="marca_id" class="form-control">
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre de la modelo</label>
                            <input type="text" class="form-control" name="nombre" required>
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

    {{-- marca  --}}
    <div class="modal fade" id="marcaModal" tabindex="-1" role="dialog" aria-labelledby="crearMarcaModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <form action="{{ route('admin.marcas.store') }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="crearMarcaModalLabel"><b>Agregar Nueva Marca</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nombre">Nombre de la Marca</label>
                    <input type="text" class="form-control" name="nombre" required>
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

@stop

@section('css')

    @include('admin/equipos/parciales/css')

@section('js')

    @include('admin/equipos/parciales/js')
  
@stop
