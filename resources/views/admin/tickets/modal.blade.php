<div class="modal fade" id="modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="form">
                @csrf
                <input type="hidden" name="id" id="id">

                <div class="modal-header">
                    <h5>Ticket</h5>
                </div>

                <div class="modal-body">

                    <input type="text" name="cliente" placeholder="Cliente" class="form-control mb-2">
                    <input type="text" name="empresa" placeholder="Empresa" class="form-control mb-2">
                    <input type="text" name="equipo" placeholder="Equipo" class="form-control mb-2">

                    <textarea name="problema" class="form-control mb-2" placeholder="Problema"></textarea>

                    <select name="prioridad" class="form-control">
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                    </select>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Guardar</button>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- modal Asignacion  --}}
<div class="modal fade" id="modalAsignar">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formAsignar">
                @csrf

                <input type="hidden" name="ticket_id" id="ticket_id">

                <div class="modal-body">
                    <select name="user_id" class="form-control">
                        @foreach($tecnicos as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Asignar</button>
                </div>

            </form>

        </div>
    </div>
</div>

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