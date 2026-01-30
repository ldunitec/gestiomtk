@extends('adminlte::page')

@section('content_header')
    <h1><b>administrativo/vista de ficha administrativo</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Lista de datos</h3>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                   

                        {{-- Select rol   --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Selecciones un rol</label><b> (*)</b>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-award"></i></span>
                                        <select name="rol" id="" class="form-control" disabled>
                                            <option value="">Seleccione un rol</option>
                                            @foreach ($roles as $rol)
                                                {{-- <option value="{{ $rol->name }}">{{ $rol->name }}</option> --}}
                                                <option value="{{ $rol->name }}" {{ ($administrativo->usuario->roles->pluck('name')->implode(', ') == $rol->name) ? 'selected' : '' }}>
                                                    {{ $rol->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @error('rol')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            {{-- nombres --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Nombre del personal administrativo</label><b> (*)</b>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="nombres"
                                            value="{{ old('nombres', $administrativo->nombres) }}"
                                            placeholder="Escriba aquí..." disabled >
                                    </div>
                                    @error('nombres')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            {{-- Apellidos --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="apellidos">Apellidos</label><b> (*)</b>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="apellidos"
                                            value="{{ old('apellidos', $administrativo->apellidos) }}"
                                            placeholder="Escriba los apellidos..." disabled>
                                    </div>
                                    @error('apellidos')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- DNI --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dni">DNI</label><b> (*)</b>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="dni"
                                            value="{{ old('dni', $administrativo->dni) }}" placeholder="Escriba el DNI..."
                                            disabled>
                                    </div>
                                    @error('dni')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                            {{-- Fecha de Nacimiento --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_nacimiento">Fecha de Nacimiento</label><b> (*)</b>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_nacimiento"
                                            value="{{ old('fecha_nacimiento', $administrativo->fecha_nacimiento) }}"
                                            disabled>
                                    </div>
                                    @error('fecha_nacimiento')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                            {{-- Teléfono --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label><b> (*)</b>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="tel" class="form-control" name="telefono"
                                            value="{{ old('telefono', $administrativo->telefono) }}"
                                            placeholder="Escriba el teléfono..." disabled>
                                    </div>
                                    @error('telefono')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{-- Correo --}}
                        <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label><b> (*)</b>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="tel" class="form-control" name="telefono"
                                        value="{{ old('telefono', $administrativo->usuario->email) }}" placeholder="Escriba el teléfono..." disabled>
                                </div>
                                @error('telefono')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>


                        {{-- Profesión --}}

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="profesion">Profesión</label><b> (*)</b>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="profesion"
                                        value="{{ old('profesion', $administrativo->profesion) }}"
                                        placeholder="Escriba la profesión..." disabled>
                                </div>
                                @error('profesion')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>


                        {{-- Dirección --}}

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="direccion">Dirección</label><b> (*)</b>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-home"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="direccion"
                                        value="{{ old('direccion', $administrativo->direccion) }}"
                                        placeholder="Escriba la dirección..." disabled>
                                </div>
                                @error('direccion')
                                    <small style="color: red">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <a href="{{ url('/admin/administrativos') }}" class="btn btn-secondary">Regresar</a>
                       
                        </div>
                    </div>
                </div>
           
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    </div>
@stop

@section('css')

@stop

@section('js')

@stop
