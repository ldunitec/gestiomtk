<div class="modal fade" id="crearPatch_panelModal" tabindex="-1" role="dialog" aria-labelledby="crearPatch_panelModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.patch_panels.store') }}" method="POST">
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
