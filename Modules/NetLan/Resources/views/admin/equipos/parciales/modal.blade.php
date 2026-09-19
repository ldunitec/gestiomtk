<div class="modal fade" id="crearEquipoModal"  role="dialog" aria-labelledby="crearEmpleadoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('netlan.admin.equipos.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="crearEmpleadoModalLabel">Agregar Nuevo equipo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="col-md-12">
                        {{-- nombres  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Asignado a:</label>
                                <select name="empleado_id" id="" class="form-control select2 select_emp"
                                    style="width: 100%">
                                    <option value="">Seleciones ...</option>
                                    @foreach ($empleados as $empleado)
                                        <option value="{{ $empleado->id }}">
                                            {{ $empleado->nombres . ' ' . $empleado->apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        {{-- apellidos  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Nombre del equipo</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                        </div>
                        {{-- serial_number  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="serial_number">No. Serie</label>
                                <input type="text" class="form-control" name="serial_number" required>
                            </div>
                        </div>



                        {{-- ip_address  --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ip_address">ip_address</label>
                                    <input type="text" class="form-control" name="ip_address" required>
                                </div>
                            </div>

                            {{--  mac_address  --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mac_address">Mac Address</label>
                                    <input type="text" class="form-control" name="mac_address" required>
                                </div>
                            </div>
                            {{--  puerto_patch   --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipo">Tipo </label>
                                   <select name="tipo" id="" class="form-control">
                                    <option value="PC">PC</option>
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
                                            <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- modelo_id --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modelo_id">Modelo</label>
                                    <select id="modelo" name="modelo_id" class="form-control" style="width: 100%">
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
                                        <option value="N/A">N/A</option>
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
                                    <select name="procesador" id="" class="form-control">
                                        <option value="Activo">i5</option>
                                        <option value="Inactivo">i3</option>
                                        <option value="Inactivo">i7</option>
                                        <option value="Inactivo">i9</option>
                                        <option value="Inactivo">Rizen 5</option>
                                        <option value="Inactivo">Rizen 7</option>
                                        <option value="N/A">N/A</option>
                                    </select>
                                </div>
                            </div>
                            {{-- ram  --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ram"> ram</label>
                                    <select name="ram" id="" class="form-control">
                                        <option value="4 GB">4 GB</option>
                                        <option value="5 GB">6 GB</option>
                                        <option value="8 GB">8 GB</option>
                                        <option value="16 GB">16 GB</option>
                                        <option value="24 GB">24 GB</option>
                                        <option value="32 GB">32 GB</option>
                                        <option value="N/A">N/A</option>
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
                                        <option value="N/A">N/A</option>
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


{{-- editar --}}
<div class="modal fade" id="crearEquipoModal"  role="dialog" aria-labelledby="crearEmpleadoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('netlan.admin.equipos.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="crearEmpleadoModalLabel">Agregar Nuevo equipo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="col-md-12">
                        {{-- nombres  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Asignado a:</label>
                                <select name="empleado_id" id="" class="form-control select2 select_emp"
                                    style="width: 100%">
                                    <option value="">Seleciones ...</option>
                                    @foreach ($empleados as $empleado)
                                        <option value="{{ $empleado->id }}">
                                            {{ $empleado->nombres . ' ' . $empleado->apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        {{-- apellidos  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Nombre del equipo</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                        </div>
                        {{-- serial_number  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="serial_number">No. Serie</label>
                                <input type="text" class="form-control" name="serial_number" required>
                            </div>
                        </div>



                        {{-- ip_address  --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ip_address">ip_address</label>
                                    <input type="text" class="form-control" name="ip_address" required>
                                </div>
                            </div>

                            {{--  mac_address  --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mac_address">Mac Address</label>
                                    <input type="text" class="form-control" name="mac_address" required>
                                </div>
                            </div>
                            {{--  puerto_patch   --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipo">Tipo </label>
                                   <select name="tipo" id="" class="form-control">
                                    <option value="PC">PC</option>
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
                                            <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- modelo_id --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modelo_id">Modelo</label>
                                    <select id="modelo" name="modelo_id" class="form-control" style="width: 100%">
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
                                        <option value="N/A">N/A</option>
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
                                    <select name="procesador" id="" class="form-control">
                                        <option value="Activo">i5</option>
                                        <option value="Inactivo">i3</option>
                                        <option value="Inactivo">i7</option>
                                        <option value="Inactivo">i9</option>
                                        <option value="Inactivo">Rizen 5</option>
                                        <option value="Inactivo">Rizen 7</option>
                                        <option value="N/A">N/A</option>
                                    </select>
                                </div>
                            </div>
                            {{-- ram  --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ram"> ram</label>
                                    <select name="ram" id="" class="form-control">
                                        <option value="4 GB">4 GB</option>
                                        <option value="5 GB">6 GB</option>
                                        <option value="8 GB">8 GB</option>
                                        <option value="16 GB">16 GB</option>
                                        <option value="24 GB">24 GB</option>
                                        <option value="32 GB">32 GB</option>
                                        <option value="N/A">N/A</option>
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
                                        <option value="N/A">N/A</option>
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