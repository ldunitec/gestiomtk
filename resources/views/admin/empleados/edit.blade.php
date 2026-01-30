@extends('adminlte::page')

@section('content_header')
    <h1><b>Editar empleado</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Cambie los datos del formulario</h3>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <form action="{{ url('admin/empleados') . '/' . $empleado->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <input value="{{ $empleado->id }} " type="hidden" class="form-control" name="id" required>
                        <div class="col-md-12">
                            {{-- nombres  --}}
                            <div class="col-md-12">
                                <div>
                                    <label for="nombre">Nombre del empleado</label>
                                    <input value="{{ $empleado->nombres }} " type="text" class="form-control"
                                        name="nombres" required>
                                </div>
                            </div>


                            {{-- apellidos  --}}
                            <div class="col-md-12">
                                <div>
                                    <label for="apellido">Apellido del empleado</label>
                                    <input value="{{ $empleado->apellidos }} " type="text" class="form-control"
                                        name="apellidos" required>
                                </div>
                            </div>
                            {{-- email  --}}
                            <div class="col-md-12">
                                <div>
                                    <label for="email">E-mail</label>
                                    <input value="{{ $empleado->usuario->email }} " type="email" class="form-control"
                                        name="email" required>
                                </div>
                            </div>



                            {{-- dni  --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dni">DNI</label>
                                        <input value="{{ $empleado->dni }} "type="text" class="form-control" name="dni"
                                            required>
                                    </div>
                                </div>

                                {{--  fecha_nacimiento  --}}

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_nacimiento">Fecha de nacimiento</label>
                                        <input type="date" name="fecha_nacimiento" class="form-control"
                                            value="{{ $empleado->fecha_nacimiento ? \Carbon\Carbon::parse($empleado->fecha_nacimiento)->format('Y-m-d') : '' }}">

                                    </div>
                                </div>

                            </div>


                            {{-- telefono  --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Telefono</label>
                                        <input value="{{ $empleado->telefono }} " type="text" class="form-control"
                                            name="telefono" required>
                                    </div>
                                </div>

                                {{-- departamento_id  --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="departamento_id">Departamento</label>
                                        <select name="departamento_id" id="departamento_id" class="form-control">
                                            @foreach ($departamentos as $departamento)
                                                <option value="{{ $departamento->id }}"
                                                    {{ $empleado->departamento_id == $departamento->id ? 'selected' : '' }}>
                                                    {{ $departamento->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                {{-- reloj  --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reloj">Codigo de reloj</label>
                                        <input value="{{ $empleado->reloj }} " type="text" class="form-control"
                                            name="reloj">
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
                        </div>

                        <div class="modal-footer">
                            <a href="{{ url('/admin/empleados') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Guardar</button>
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

@stop

@section('js')

@stop
