<div class="modal fade" id="crearDepartamentoModal" tabindex="-1" role="dialog" aria-labelledby="crearDepartamentoModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <form action="{{ route('admin.departamentos.store') }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="crearDepartamentoModalLabel">Agregar Nueva departamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nombre">Nombre de la departamento</label>
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


<!-- Modal Editar Departamento -->
<div class="modal fade" id="editDepartamentoModal" tabindex="-1" role="dialog" aria-labelledby="editDepartamentoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editDepartamentoForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Editar Departamento</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label for="edit_nombre">Nombre del Departamento</label>
                        <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </div>
            </div>
        </form>
    </div>
</div>

