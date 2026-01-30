{{-- modal create  --}}
<div class="modal fade" id="createModal" tabindex="" role="dialog" aria-labelledby="crearPortsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.ports.store') }}" method="POST">
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
                        {{-- localidad  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Patch panel</label>
                                <select name="patch_panel_id" id="" class="form-control select2"
                                    style="width: 100%">
                                    <option value="">Seleciones ...</option>
                                    @foreach ($patchs as $patch)
                                        <option value="{{ $patch->id }}">{{ $patch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- nombre  --}}
                        <div class="col-md-12">
                            <div>
                                <label for="nombre">Numero del ports</label>
                                <input type="number" class="form-control" name="port_number" required>
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

{{-- modal edit --}}

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModeloModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="editModeloModalLabel"><b>Modificar el Puerto</b></h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div>
                        <label for="nombre">Patch panel</label>
                        <select name="patch_panel_id" id="edit_patch_panel_id" class="form-control select2" style="width: 100%">
                            <option value="">Seleciones ...</option>
                            @foreach ($patchs as $patch)
                                <option value="{{ $patch->id }}">{{ $patch->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="edit_name">Nombre del Puerto</label>
                        <input type="text" class="form-control" name="port_number" id="edit_port_number">
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
