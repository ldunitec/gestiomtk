<div class="modal fade" id="modalEvidencias">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Evidencias</h5>
            </div>

            <div class="modal-body">

                <form id="formEvidencia" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="ticket_id" id="ticket_evidencia">

                    <input type="file" name="archivo" class="form-control mb-2">
                    <input type="text" name="descripcion" class="form-control mb-2" placeholder="Descripción">

                    <button class="btn btn-success">Subir</button>
                </form>

                <hr>

                <table class="table" id="tablaEvidencias">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Descripción</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>

        </div>
    </div>
</div>