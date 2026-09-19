@extends('adminlte::page')

@section('content_header')
    <h1><b>materia/Editar Equipo</b></h1>
    <hr>
@stop

@section('content')

    <div class="col-md-8">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Editar equipo</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                {{-- editar --}}

                <form action="{{ url('admin/equipos', parameters: $equipo->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="col-md-12">
                        {{-- nombres  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Asignado a:</label>
                                <select name="empleado_id" id="select_emp" class="form-control select2">
                                    <option value="">Seleciones ...</option>
                                    @foreach ($empleados as $empleado)
                                        <option value="{{ $empleado->id }}"
                                            {{ $equipo->empleado->id == $empleado->id ? 'selected' : '' }}>
                                            {{ $empleado->nombres . ' ' . $empleado->apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        {{-- NOmbre del equipo  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Nombre del equipo</label>
                                <input class="form-control" type="text" value="{{ $equipo->nombre }}" name="nombre"
                                    required>
                            </div>
                        </div>
                        {{-- serial_number  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="serial_number">No. Serie</label>
                                <input type="text" class="form-control" value="{{ $equipo->serial_number }}"
                                    name="serial_number" required>
                            </div>
                        </div>



                        {{-- ip_address  --}}
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="ip_address">ip_address</label>
                                    <input type="text" class="form-control" value="{{ $equipo->ip_address }}"
                                        name="ip_address" required>
                                </div>
                            </div>

                            {{--  mac_address  --}}

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="mac_address">Mac Address</label>
                                    <input type="text" class="form-control" value="{{ $equipo->mac_address }}"
                                        name="mac_address" required>
                                </div>
                            </div>
                            {{-- Tipo  --}}

                       <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipo">Tipo </label>
                                   <select name="tipo" id="" class="form-control">
                                    <option value="{{ $equipo->tipo }}">{{ $equipo->tipo }}</option>
                                    <option value="PC">PC</option>
                                    <option value="ROUTER">ROUTER</option>
                                    <option value="TELEFONO">TELEFONO</option>
                                    <option value="IMPRESORA">IMPRESORA</option>
                                    <option value="CAMARA">CAMARA</option>
                                    <option value="BIOMETRICO">BIOMETRICO</option>
                                    <option value="POS">POS</option>
                                   </select>
                                </div>
                            </div>

                        </div>



                        {{-- marca_id  --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="marca_id">Marca</label>
                                    <select name="marca_id" id="marca_id" class="form-control">
                                        @foreach ($marcas as $marca)
                                            <option value="{{ $marca->id }}"
                                                {{ $equipo->marca->id == $marca->id ? 'selected' : '' }}>
                                                {{ $marca->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- modelo_id --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modelo_id">Modelo</label>
                                    <select name="modelo_id" id="modelo_db" class="form-control" style="display: block"  >
                                        @foreach ($modelos as $modelo)
                                            <option value="{{ $modelo->id }}"
                                                {{ $equipo->modelo_id == $modelo->id ? 'selected' : '' }} readonly >
                                                {{ $modelo->nombre }}</option>
                                        @endforeach
                                    </select>

                                    <select name="modelo_id" id="modelo" class="form-control " style="display: none">
                                        <option value="">Seleccione un modelo</option>
                                    </select>
                                </div>
                            </div>

                            {{-- antivirus  --}}

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="antivirus">Antivirus</label>
                                    <select name="antivirus" id="" class="form-control">
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            {{-- estado  --}}

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estado">Estado</label>
                                    <select name="estado" id="" class="form-control">
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        {{-- procesador  --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="procesador"> procesador</label>
                                    <select name="procesador" id="procesador" class="form-control">
                                        <option value="i5" {{ $equipo->procesador == 'i5' ? 'selected' : '' }}>i5
                                        </option>
                                        <option value="i3" {{ $equipo->procesador == 'i3' ? 'selected' : '' }}>i3
                                        </option>
                                        <option value="i7" {{ $equipo->procesador == 'i7' ? 'selected' : '' }}>i7
                                        </option>
                                        <option value="i9" {{ $equipo->procesador == 'i9' ? 'selected' : '' }}>i9
                                        </option>
                                        <option value="Rizen 5" {{ $equipo->procesador == 'Rizen 5' ? 'selected' : '' }}>
                                            Rizen 5</option>
                                        <option value="Rizen 7" {{ $equipo->procesador == 'Rizen 7' ? 'selected' : '' }}>
                                            Rizen 7</option>
                                    </select>
                                </div>
                            </div>
                            {{-- ram  --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ram"> ram</label>

                                    <select name="ram" id="procesador" class="form-control">
                                        <option value="4 GB" {{ $equipo->ram == '4 GB' ? 'selected' : '' }}>4 GB
                                        </option>
                                        <option value="5 GB" {{ $equipo->ram == '5 GB' ? 'selected' : '' }}>5 GB
                                        </option>
                                        <option value="8 GB" {{ $equipo->ram == '8 GB' ? 'selected' : '' }}>8 GB
                                        </option>
                                        <option value="16 GB" {{ $equipo->ram == '16 GB' ? 'selected' : '' }}>16 GB
                                        </option>
                                        <option value="24 GB" {{ $equipo->ram == '24 GB ' ? 'selected' : '' }}>24 GB
                                        </option>
                                        <option value="32 GB" {{ $equipo->ram == '32 GB ' ? 'selected' : '' }}>32 GB
                                        </option>
                                    </select>
                                </div>
                            </div>
                            {{-- almacenamiento  --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="almacenamiento"> almacenamiento</label>
                                    <select name="almacenamiento" id="" class="form-control">
                                        <option value="240 SSD">240 SSD</option>
                                        <option value="500 SSD">500 SSD</option>
                                        <option value="500 HDD">500 HDD</option>
                                        <option value="1TB SDD">1TB SDD</option>
                                        <option value="1TB HHD">1TB HHD</option>
                                        <option value="2TB SSD">2TB SSD</option>
                                        <option value="2TB SSD">2TB SSD</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <a href="{{ url('/admin/equipos') }}" class="btn btn-secondary">Cancelar</a>
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


@stop

@section('css')

@include('admin.equipos.parciales.css')

@stop

@section('js')
@include('admin.equipos.parciales.js')
 
@stop
