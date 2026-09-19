@extends('adminlte::page')

@section('content')

    <button class="btn btn-primary mb-3" id="btnNuevo">Nuevo Egreso</button>

    <table class="table table-bordered" id="tabla">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>

    <!-- MODAL -->
    <div class="modal fade" id="modalEgreso">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formEgreso">
                    @csrf
                    <input type="hidden" id="id">

                    <div class="modal-header">
                        <h5>Egreso</h5>
                    </div>

                    <div class="modal-body">

                        <input type="text" name="descripcion" id="descripcion" class="form-control mb-2"
                            placeholder="Descripción">

                        <input type="number" name="monto" id="monto" class="form-control mb-2" placeholder="Monto">

                        <input type="date" name="fecha" id="fecha" class="form-control mb-2">

                        <select name="categoria_id" id="categoria" class="form-control">
                            @foreach ($categorias as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Guardar</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@stop

@section('js')

    <script>
        let tabla = $('#tabla').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('egresos.data') }}",
             dataSrc: function(json) {
            console.log("Respuesta completa:", json);
            return json.data;
        },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'descripcion'
                },
                {
                    data: 'monto'
                },
                {
                    data: 'fecha'
                },
                {
                    data: 'categoria'
                },
                {
                    data: 'acciones'
                }
            ]
        });

        // NUEVO
        $('#btnNuevo').click(function() {
            $('#formEgreso')[0].reset();
            $('#id').val('');
            $('#modalEgreso').modal('show');
        });

        // GUARDAR
        $('#formEgreso').submit(function(e) {
            e.preventDefault();

            let id = $('#id').val();

            let url = id == '' ?
                "{{ route('egresos.store') }}" :
                "/finanzas/egresos/" + id;

            let method = id == '' ? "POST" : "PUT";

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function() {
                    $('#modalEgreso').modal('hide');
                    tabla.ajax.reload();

                    Swal.fire('OK', 'Guardado correctamente', 'success');
                }
            });

        });

        // EDITAR
        $('#tabla').on('click', '.editar', function() {

            let id = $(this).data('id');

            $.get('/finanzas/egresos/' + id + '/edit', function(data) {

                $('#id').val(data.id);
                $('#descripcion').val(data.descripcion);
                $('#monto').val(data.monto);
                $('#fecha').val(data.fecha);
                $('#categoria').val(data.categoria_id);

                $('#modalEgreso').modal('show');
            });

        });

        // ELIMINAR
        $('#tabla').on('click', '.eliminar', function() {

            let id = $(this).data('id');

            Swal.fire({
                title: '¿Eliminar?',
                showCancelButton: true
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: '/admin/finanzas/egresos/' + id,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            tabla.ajax.reload();
                            Swal.fire('Eliminado', '', 'success');
                        }
                    });

                }

            });

        });
    </script>

@stop
