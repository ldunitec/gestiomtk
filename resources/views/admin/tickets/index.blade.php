@extends('adminlte::page')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-primary mb-3" id="btnNuevo">Nuevo Ticket</button>


            <table id="tabla" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Empresa</th>
                        <th>Equipo</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>


    @include('admin.tickets.modal')
@endsection

@section('js')
    <script>
        let tabla = $('#tabla').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.tickets.listar') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                }
            },
            columns: [{
                    data: 'codigo'
                },
                {
                    data: 'cliente'
                },
                {
                    data: 'empresa'
                },
                {
                    data: 'equipo'
                },
                {
                    data: 'prioridad'
                },
                {
                    data: 'estado'
                },
                {
                    data: 'acciones'
                }
            ]
        });
    </script>
    <script>
        $('#btnNuevo').click(() => {
            $('#form')[0].reset();
            $('#id').val('');
            $('#modal').modal('show');
        });

        $('#form').submit(function(e) {
            e.preventDefault();

            let url = $('#id').val() ? "{{ route('admin.tickets.update') }}" : "{{ route('admin.tickets.store') }}";

            $.post(url, $(this).serialize(), function() {
                $('#modal').modal('hide');
                tabla.ajax.reload();
            });
        });

        $(document).on('click', '.editar', function() {
            let id = $(this).data('id');

            $.get("tickets/edit/" + id, function(data) {
                $('#id').val(data.id);
                $('[name="cliente"]').val(data.cliente);
                $('[name="empresa"]').val(data.empresa);
                $('[name="equipo"]').val(data.equipo);
                $('[name="problema"]').val(data.problema);
                $('[name="prioridad"]').val(data.prioridad);

                $('#modal').modal('show');
            });
        });

        $(document).on('click', '.eliminar', function() {
            let id = $(this).data('id');

            if (confirm('Eliminar?')) {
                $.post("tickets/delete", {
                    _token: "{{ csrf_token() }}",
                    id: id
                }, function() {
                    tabla.ajax.reload();
                });
            }
        });


        // abrir modal
        $(document).on('click', '.asignar', function() {
            $('#ticket_id').val($(this).data('id'));
            $('#modalAsignar').modal('show');
        });

        // asignar
        $('#formAsignar').submit(function(e) {
            e.preventDefault();

            $.post("{{ route('admin.tickets.asignar') }}", $(this).serialize(), function() {
                $('#modalAsignar').modal('hide');
                tabla.ajax.reload();
            });
        });

        // iniciar
        $(document).on('click', '.iniciar', function() {
            $.post("{{ route('admin.tickets.iniciar') }}", {
                _token: "{{ csrf_token() }}",
                ticket_id: $(this).data('id')
            }, function() {
                tabla.ajax.reload();
            });
        });

        // finalizar
        $(document).on('click', '.finalizar', function() {
            $.post("{{ route('admin.tickets.finalizar') }}", {
                _token: "{{ csrf_token() }}",
                ticket_id: $(this).data('id')
            }, function() {
                tabla.ajax.reload();
            });
        });

        // abrir modal
        $(document).on('click', '.evidencias', function() {
            let id = $(this).data('id');

            $('#ticket_evidencia').val(id);
            cargarEvidencias(id);

            $('#modalEvidencias').modal('show');
        });

        // cargar evidencias
        function cargarEvidencias(ticket_id) {

            $.get("/admin/evidencias/" + ticket_id, function(data) {

                let html = '';

                data.forEach(e => {

                    let url = "/admin/evidencias/" + e.archivo;

                    html += `
                <tr>
                    <td><a href="${url}" target="_blank">Ver</a></td>
                    <td>${e.descripcion ?? ''}</td>
                    <td>
                        <button class="btn btn-danger btn-sm eliminarEvidencia" data-id="${e.id}">X</button>
                    </td>
                </tr>
            `;
                });

                $('#tablaEvidencias tbody').html(html);
            });
        }

        // subir evidencia
        $('#formEvidencia').submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.evidencias.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    cargarEvidencias($('#ticket_evidencia').val());
                    $('#formEvidencia')[0].reset();
                }
            });
        });

        // eliminar evidencia
        $(document).on('click', '.eliminarEvidencia', function() {

            let id = $(this).data('id');

            $.post("{{ route('admin.evidencias.delete') }}", {
                _token: "{{ csrf_token() }}",
                id: id
            }, function() {
                cargarEvidencias($('#ticket_evidencia').val());
            });

        });


        setInterval(() => {
            document.querySelectorAll('.tiempo').forEach(el => {
                let tiempo = parseInt(el.dataset.tiempo);

                if (tiempo > 0) {
                    tiempo--;
                    el.dataset.tiempo = tiempo;
                    el.innerText = tiempo + 'h';

                    if (tiempo <= 0) {
                        el.classList.remove('bg-warning');
                        el.classList.add('bg-danger');
                        el.innerText = 'Vencido';
                    }
                }
            });
        }, 3600000); // cada hora (puedes cambiar a 60000 para pruebas)


        // colorea si esta vencida
      
    </script>
@endsection
