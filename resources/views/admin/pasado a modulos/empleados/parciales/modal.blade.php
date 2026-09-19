<div class="modal fade" id="crearEmpleadoModal" tabindex="-1" role="dialog" aria-labelledby="crearEmpleadoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.empleados.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="crearEmpleadoModalLabel">Agregar Nueva empleado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="col-md-12">
                        {{-- nombres  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Nombre de la empleado</label>
                                <input type="text" class="form-control" name="nombres" required>
                            </div>
                        </div>


                        {{-- apellidos  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="apellido">Apellido de la empleado</label>
                                <input type="text" class="form-control" name="apellidos" required>
                            </div>
                        </div>
                           {{-- email  --}}
                           <div class="col-md-12">
                            <div>
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>



                        {{-- dni  --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dni">DNI</label>
                                    <input type="text" class="form-control" name="dni" required>
                                </div>
                            </div>

                            {{--  fecha_nacimiento  --}}

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_nacimiento">Fecha de nacimiento</label>
                                    <input type="date" class="form-control" name="fecha_nacimiento" required>
                                </div>
                            </div>

                        </div>


                        {{-- telefono  --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono">Telefono</label>
                                    <input type="text" class="form-control" name="telefono" required>
                                </div>
                            </div>

                            {{-- departamento_id  --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departamento_id">Departamento</label>
                                  <select name="departamento_id" id="" class="form-control">
                                    @foreach ($departamentos as $departamento)
                                    <option value="{{$departamento->id}}">{{$departamento->nombre}}</option>
                                    @endforeach
                                  </select>
                                </div>
                            </div>

                            {{-- reloj  --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reloj">Codigo de reloj</label>
                                    <input type="text" class="form-control" name="reloj">
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


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>



{{-- :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: --}}
<div class="modal fade" id="editEmpleadoModal" tabindex="-1" role="dialog"
aria-labelledby="editEmpleadoModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
    <form action="{{ url('admin/empleados') . '/' . $empleado->id }}"
        method="POST">
        @csrf
        @method('PUT');
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="crearempleadoModalLabel">Modificar
                     empleado</h5>
                    <button type="button" class="close" data-dismiss="modal"
                    aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    {{-- nombres  --}}
                    <div class="col-md-12">
                        <div>
                            <label for="nombre">Nombre de la empleado</label>
                            <input  value="{{ $empleado->nombres }}" type="text" class="form-control" name="nombres" required>
                        </div>
                    </div>


                    {{-- apellidos  --}}
                    <div class="col-md-12">
                        <div>
                            <label for="apellido">Apellido de la empleado</label>
                            <input type="text" class="form-control" name="apellidos" required>
                        </div>
                    </div>
                       {{-- email  --}}
                       <div class="col-md-12">
                        <div>
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>



                    {{-- dni  --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dni">DNI</label>
                                <input type="text" class="form-control" name="dni" required>
                            </div>
                        </div>

                        {{--  fecha_nacimiento  --}}

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                                <input type="date" class="form-control" name="fecha_nacimiento" required>
                            </div>
                        </div>

                    </div>


                    {{-- telefono  --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">Telefono</label>
                                <input type="text" class="form-control" name="telefono" required>
                            </div>
                        </div>

                        {{-- departamento_id  --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departamento_id">Departamento</label>
                              <select name="departamento_id" id="" class="form-control">
                                @foreach ($departamentos as $departamento)
                                <option value="{{$departamento->id}}">{{$departamento->nombre}}</option>
                                @endforeach
                              </select>
                            </div>
                        </div>

                        {{-- reloj  --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reloj">Codigo de reloj</label>
                                <input type="text" class="form-control" name="reloj">
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-dismiss="modal">Cancelar</button>
                    <button type="submit"
                    class="btn btn-primary">Guardar</button>
                </div>
        </div>
    </form>
</div>
</div>







