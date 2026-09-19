@extends('adminlte::page')

@section('title', 'Empleados')

@section('content')
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary" id="btnCrear">
                <i class="fas fa-plus"></i> Nuevo Empleado
            </button>
        </div>

        <a href="https://wa.me/50432132859?text={{ urlencode('Hola, su pago está pendiente') }}" target="_blank"
            class="btn btn-success">
            <i class="fab fa-whatsapp"></i> Enviar WhatsApp
        </a>

        <div class="card-body">
            <table class="table table-bordered table-striped" id="tablaEmpleados">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>DNI</th>
                        <th>Teléfono</th>
                        <th>Departamento</th>
                        <th>Estado</th>
                        <th width="100px">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    {{-- modal --}}
    <div class="modal fade" id="modalEmpleado" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header bg-primary">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <form id="formEmpleado">
                        @csrf
                        <input type="hidden" name="id" id="empleado_id">

                        <div class="row">

                            <div class="col-md-6">
                                <label>Nombre</label>
                                <input type="text" class="form-control" name="nombres" id="nombres" required>
                            </div>

                            <div class="col-md-6">
                                <label>Apellido</label>
                                <input type="text" class="form-control" name="apellidos" id="apellidos" required>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>DNI</label>
                                <input type="text" class="form-control" name="dni" id="dni" required>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Fecha de nacimiento</label>
                                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control">
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Teléfono</label>
                                <input type="text" class="form-control" name="telefono" id="telefono" required>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Departamento</label>
                                <select name="departamento_id" id="departamento_id" class="form-control">
                                    @foreach ($departamentos as $departamento)
                                        <option value="{{ $departamento->id }}">
                                            {{ $departamento->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Código de reloj</label>
                                <input type="text" class="form-control" name="reloj" id="reloj">
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Estado</label>
                                <select name="estado" id="estado" class="form-control">
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" form="formEmpleado" class="btn btn-success">
                        Guardar
                    </button>
                </div>

            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        let tabla;

        $(function() {

            tabla = $("#tablaEmpleados").DataTable({
                pageLength: 5,
                language: {
                    emptyTable: "No hay información",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ empleados",
                    infoEmpty: "Mostrando 0 a 0 de 0 empleados",
                    infoFiltered: "(Filtrado de _MAX_ total empleados)",
                    lengthMenu: "Mostrar _MENU_ empleados",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscador:",
                    zeroRecords: "Sin resultados encontrados",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                },
                responsive: false,
                scrollX: true,
                lengthChange: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.empleados.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'nombres',
                        name: 'nombres'
                    },
                    {
                        data: 'apellidos',
                        name: 'apellidos'
                    },
                    {
                        data: 'dni',
                        name: 'dni'
                    },
                    {
                        data: 'telefono',
                        name: 'telefono'
                    },
                    {
                        data: 'departamento',
                        name: 'departamento.nombre'
                    },
                    {
                        data: 'estado_badge',
                        name: 'estado'
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

        });

        // ✅ BOTÓN CREAR
        $('#btnCrear').click(function() {
            limpiarFormulario();
            $('.modal-title').text('Nuevo Empleado');
            $('#modalEmpleado').modal('show');
        });
        // ✅ BOTÓN EDITAR
        $(document).on('click', '.edit', function() {
            limpiarFormulario();
            $('.modal-title').text('Editar Empleado');
            $('#modalEmpleado').modal('show');

            let id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.empleados.edit', ':id') }}".replace(':id', id),
                type: 'GET',
                success: function(data) {
                    limpiarFormulario();
                    // Supongamos que obtienes 'data' desde el edit Ajax
                    $('#empleado_id').val(data.empleado.id);
                    $('#nombres').val(data.empleado.nombres);
                    $('#apellidos').val(data.empleado.apellidos);
                    $('#dni').val(data.empleado.dni);
                    $('#telefono').val(data.empleado.telefono);
                    $('#fecha_nacimiento').val(data.empleado.fecha_nacimiento);
                    $('#departamento_id').val(data.empleado.departamento_id);
                    $('#reloj').val(data.empleado.reloj);
                    $('#estado').val(data.empleado.estado);

                    // 🔹 Usuario vinculado
                    $('#email').val(data.empleado.usuario.email);
                    $('#usuario_id').val(data.empleado.usuario
                        .id); // si tienes input hidden para usuario

                    $('#modalEmpleado').modal('show');
                    console.log(url);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar el empleado'
                    });
                }
            });



        });
        // $(document).on('click', '.edit', function() {}
        // ✅ GUARDAR (CREAR / EDITAR)
        $('#formEmpleado').submit(function(e) {
            e.preventDefault();

            let id = $('#empleado_id').val();
            let url = id ?
                "{{ route('admin.empleados.update', ':id') }}".replace(':id', id) :
                "{{ route('admin.empleados.store') }}";

            let method = id ? 'PUT' : 'POST';
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),

                success: function(response) {
                    $('#modalEmpleado').modal('hide');
                    tabla.ajax.reload();

                    Swal.fire({
                        icon: 'success',
                        title: '¡Correcto!',
                        text: id ? 'Empleado actualizado correctamente' :
                            'Empleado creado correctamente',
                        showConfirmButton: false,
                        timer: 1800
                    });

                },

                error: function(xhr) {

                    $('.text-danger').text('');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(key, value) {
                            $('.error-' + key).text(value[0]);
                        });

                        Swal.fire({
                            icon: 'warning',
                            title: 'Errores de validación',
                            text: 'Revisa los campos obligatorios'
                        });

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un problema en el servidor'
                        });

                    }
                }
            });
        });


        // ✅ CONFIRMAR ELIMINAR (SI TIENES BOTÓN DELETE)
        $(document).on('click', '.delete', function() {

            let id = $(this).data('id');

            Swal.fire({
                title: '¿Eliminar empleado?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: `/empleados/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function() {
                            tabla.ajax.reload();

                            Swal.fire(
                                'Eliminado!',
                                'El empleado fue eliminado.',
                                'success'
                            );
                        }
                    });

                }
            });
        });

        function limpiarFormulario() {
            $('#formEmpleado')[0].reset();
            $('#empleado_id').val('');
            $('.text-danger').text('');
        }
    </script>
@stop
