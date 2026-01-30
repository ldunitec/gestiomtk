<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="crearMarcaModalLabel"
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

{{-- modal editar --}}


<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
     <form id="editForm" method="POST">
        @csrf
        @method('PUT');
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="editMarcaModalLabel"><b>Editar Marca</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label for="nombre">Nombre de la Marca</label>
                    <input type="text" class="form-control" name="nombre"id="edit_nombre" required>
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
