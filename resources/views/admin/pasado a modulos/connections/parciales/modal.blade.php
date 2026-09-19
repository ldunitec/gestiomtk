{{-- create modal --}}
<div class="modal fade" id="crearPortsModal" role="dialog" aria-labelledby="crearPortsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.connections.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="crearportsModalLabel">Agregar Nuevo ports</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="col-md-12">

                        {{-- nombre  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Nombre del equipo</label>
                                <select id="selectNombre" name="equipos_id" class="form-control selectNombre select2"
                                    style="width: 100%">
                                    {{-- <select name="equipos_id" id="selectEquipo" class="form-control select2"
                                style="width: 100%"> --}}
                                    <option value="">Selecciones.....</option>
                                    @foreach ($equipos as $equipo)
                                        <option value="{{ $equipo->id }}">{{ $equipo->nombre }} /
                                            {{ $equipo->ip_address }}</option>
                                    @endforeach
                                </select>
                                @error('equipos_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        {{-- PATCH PANEL  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Puerto Patch panel</label>
                                <select name="port_id" id="selectPatch" class="form-control  selectPatch select2"
                                    style="width: 100%">
                                    <option value="">Seleciones ...</option>
                                    @foreach ($ports as $port)
                                        <option value="{{ $port->id }}"
                                            data-nombrepatch="{{ $port->Patch_panel->name }}-{{ $port->port_number }}">
                                            {{ $port->Patch_panel->name }}-{{ $port->port_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- SWITCH --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Puerto Switch</label>
                                <select name="" id="" class="form-control selectsw select2"
                                    style="width: 100%">
                                    <option value="">Seleciones ...</option>
                                    @foreach ($swPorts as $swRout)
                                        <option value="{{ $swRout->id }}"
                                            data-nombresw="{{ $swRout->sw_mk->name }}-{{ $swRout->sw_port_number }}">
                                            {{ $swRout->sw_mk->name }}-{{ $swRout->sw_port_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- comentario --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="comentario">Comentario</label>
                                <input type="text" class="form-control" name="comentario">
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
                        <hr>

                        <div class="col-md-12">
                            <input type="text" id="nombrepatch" value="" name="nombre" hidden>
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <input type="text" id="nombresw" value="" name="sw">
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

{{-- :::::::::::::::::::::::::::::::::::::::::MODAL INFORMACION DE PUERTO DE CONEXION ::::::::::::::::::::::::::::::::::: --}}

<!-- Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="infoModalLabel">Información del Puerto </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="modalContent">Cargando...</p>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
