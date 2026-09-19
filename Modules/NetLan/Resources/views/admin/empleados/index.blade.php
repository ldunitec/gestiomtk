@extends('adminlte::page')

@section('title', 'Gestión de Empleados')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-users text-primary mr-2"></i>Gestión de Empleados</h1>
            <small class="text-muted">Administración y control de personal registrado en el sistema</small>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm rounded-pill px-3" id="btnCrear">
                <i class="fas fa-user-plus mr-1"></i> Nuevo Empleado
            </button>
            <a href="https://wa.me/50432132859?text={{ urlencode('Hola, su pago está pendiente') }}" target="_blank"
                class="btn btn-success shadow-sm rounded-pill px-3 ml-1">
                <i class="fab fa-whatsapp mr-1"></i> Notificar Pago
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- Tarjetas Estadísticas --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box shadow-sm rounded-lg">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-id-card"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Total Empleados</span>
                    <span class="info-box-number h5 font-weight-bold mb-0" id="statTotal">-</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box shadow-sm rounded-lg">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Activos</span>
                    <span class="info-box-number h5 font-weight-bold mb-0" id="statActivos">-</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box shadow-sm rounded-lg">
                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-user-slash"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Inactivos</span>
                    <span class="info-box-number h5 font-weight-bold mb-0" id="statInactivos">-</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Datos --}}
    <div class="card card-outline card-primary shadow-sm border-0 rounded-lg">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle w-100" id="tablaEmpleados">
                    <thead class="bg-light">
                        <tr>
                            <th width="50px">#</th>
                            <th>Empleado</th>
                            <th>DNI</th>
                            <th>Reloj</th>
                            <th>Departamento</th>
                            <th>Puesto</th>
                            <th>Estado</th>
                            <th width="120px" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Formulario --}}
    <div class="modal fade" id="modalEmpleado" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-lg">

                <!-- Header -->
                <div class="modal-header bg-gradient-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="modalTitle">
                        <i class="fas fa-user-edit mr-2"></i><span>Nuevo Empleado</span>
                    </h5>
                    <button type="button" class="close text-white opacity-8" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body con Pestañas -->
                <div class="modal-body p-4">
                    <form id="formEmpleado" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="empleado_id">
                        <input type="hidden" name="usuario_id" id="usuario_id">

                        {{-- Navegación Pestañas --}}
                        <ul class="nav nav-pills nav-justified mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active font-weight-bold" id="tab-personales" data-toggle="pill"
                                    href="#pane-personales">
                                    <i class="fas fa-user mr-1"></i> Datos Personales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" id="tab-laborales" data-toggle="pill"
                                    href="#pane-laborales">
                                    <i class="fas fa-briefcase mr-1"></i> Info Laboral
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" id="tab-documentos" data-toggle="pill"
                                    href="#pane-documentos">
                                    <i class="fas fa-images mr-1"></i> Fotos & Documentos
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content border-top pt-3" id="pills-tabContent">

                            {{-- Pestaña 1: Datos Personales --}}
                            <div class="tab-pane fade show active" id="pane-personales">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">Nombres <span class="text-danger">*</span></label>
                                        <input type="text" name="nombres" id="nombres" class="form-control"
                                            placeholder="Ej. Juan Carlos">
                                        <div class="invalid-feedback error-nombres"></div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">Apellidos <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="apellidos" id="apellidos" class="form-control"
                                            placeholder="Ej. Pérez López">
                                        <div class="invalid-feedback error-apellidos"></div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">Correo Electrónico <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i
                                                        class="fas fa-envelope"></i></span></div>
                                            <input type="email" name="email" id="email" class="form-control"
                                                placeholder="correo@ejemplo.com">
                                            <div class="invalid-feedback error-email"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">Identificación / DNI</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i
                                                        class="fas fa-address-card"></i></span></div>
                                            <input type="text" name="dni" id="dni" class="form-control"
                                                placeholder="0801-1990-00000">
                                            <div class="invalid-feedback error-dni"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">Teléfono</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i
                                                        class="fas fa-phone"></i></span></div>
                                            <input type="text" name="telefono" id="telefono" class="form-control"
                                                placeholder="+504 9999-9999">
                                            <div class="invalid-feedback error-telefono"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">Fecha de Nacimiento</label>
                                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                                            class="form-control">
                                        <div class="invalid-feedback error-fecha_nacimiento"></div>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label class="font-weight-normal">Dirección</label>
                                        <input type="text" name="direccion" id="direccion" class="form-control"
                                            placeholder="Col. Centro, Calle Principal...">
                                        <div class="invalid-feedback error-direccion"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Pestaña 2: Info Laboral --}}
                            <div class="tab-pane fade" id="pane-laborales">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">Departamento</label>
                                        <select name="departamento_id" id="departamento_id"
                                            class="form-control custom-select">
                                            <option value="">-- Seleccionar Departamento --</option>
                                            @foreach ($departamentos as $dep)
                                                <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback error-departamento_id"></div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">Puesto</label>
                                        <input type="text" name="puesto" id="puesto" class="form-control"
                                            placeholder="Ej. Desarrollador Web">
                                        <div class="invalid-feedback error-puesto"></div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">ID Biométrico / Reloj</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i
                                                        class="fas fa-fingerprint"></i></span></div>
                                            <input type="text" name="reloj" id="reloj" class="form-control"
                                                placeholder="ID de Asistencia">
                                            <div class="invalid-feedback error-reloj"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">Fecha de Inicio Laboral</label>
                                        <input type="date" name="fecha_inicio" id="fecha_inicio"
                                            class="form-control">
                                        <div class="invalid-feedback error-fecha_inicio"></div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-normal">Estado</label>
                                        <select name="estado" id="estado" class="form-control custom-select">
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>
                                        <div class="invalid-feedback error-estado"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Pestaña 3: Fotos & Documentos --}}
                            <div class="tab-pane fade" id="pane-documentos">
                                <div class="row text-center">

                                    <!-- Foto Perfil -->
                                    <div class="col-md-4 mb-3">
                                        <label class="d-block font-weight-bold text-muted mb-2">Foto Perfil</label>
                                        <div class="image-upload-wrapper mx-auto position-relative rounded shadow-sm overflow-hidden"
                                            style="width: 140px; height: 170px; border: 2px dashed #ddd; cursor: pointer;"
                                            onclick="$('#foto').click();">
                                            <img id="previewFoto" src="{{ asset('storage/empleados/no-user.jpg') }}"
                                                class="w-100 h-100" style="object-fit: cover;">
                                            <div class="overlay-hover position-absolute w-100 h-100 text-white d-flex flex-column align-items-center justify-content-center"
                                                style="top: 0; left: 0; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.2s;">
                                                <i class="fas fa-camera fa-2x mb-1"></i>
                                                <small>Cambiar Foto</small>
                                            </div>
                                        </div>
                                        <input type="file" name="foto" id="foto" class="d-none"
                                            accept="image/*">
                                        <div class="invalid-feedback error-foto d-block"></div>
                                    </div>

                                    <!-- Foto DNI -->
                                    <div class="col-md-4 mb-3">
                                        <label class="d-block font-weight-bold text-muted mb-2">Foto DNI</label>
                                        <div class="image-upload-wrapper mx-auto position-relative rounded shadow-sm overflow-hidden"
                                            style="width: 180px; height: 170px; border: 2px dashed #ddd; cursor: pointer;"
                                            onclick="$('#fotoDni').click();">
                                            <img id="previewDni" src="{{ asset('storage/empleados/no_dni.jpg') }}"
                                                class="w-100 h-100" style="object-fit: cover;">
                                            <div class="overlay-hover position-absolute w-100 h-100 text-white d-flex flex-column align-items-center justify-content-center"
                                                style="top: 0; left: 0; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.2s;">
                                                <i class="fas fa-id-card fa-2x mb-1"></i>
                                                <small>Subir DNI</small>
                                            </div>
                                        </div>
                                        <input type="file" name="fotoDni" id="fotoDni" class="d-none"
                                            accept="image/*">
                                        <div class="invalid-feedback error-fotoDni d-block"></div>
                                    </div>

                                    <!-- Foto Licencia -->
                                    <div class="col-md-4 mb-3">
                                        <label class="d-block font-weight-bold text-muted mb-2">Foto Licencia</label>
                                        <div class="image-upload-wrapper mx-auto position-relative rounded shadow-sm overflow-hidden"
                                            style="width: 180px; height: 170px; border: 2px dashed #ddd; cursor: pointer;"
                                            onclick="$('#fotoLic').click();">
                                            <img id="previewLic" src="{{ asset('storage/empleados/no_lic.png') }}"
                                                class="w-100 h-100" style="object-fit: cover;">
                                            <div class="overlay-hover position-absolute w-100 h-100 text-white d-flex flex-column align-items-center justify-content-center"
                                                style="top: 0; left: 0; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.2s;">
                                                <i class="fas fa-id-badge fa-2x mb-1"></i>
                                                <small>Subir Licencia</small>
                                            </div>
                                        </div>
                                        <input type="file" name="fotoLic" id="fotoLic" class="d-none"
                                            accept="image/*">
                                        <div class="invalid-feedback error-fotoLic d-block"></div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                        data-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formEmpleado" id="btnGuardar"
                        class="btn btn-success rounded-pill px-4">
                        <i class="fas fa-save mr-1"></i> <span id="btnGuardarText">Guardar Empleado</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .image-upload-wrapper:hover .overlay-hover {
            opacity: 1 !important;
        }

        .nav-pills .nav-link.active {
            background-color: #007bff;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
        }
    </style>
@stop

@section('js')
    <script>
        window.baseUrl = "{{ asset('storage') }}/";
        let tabla;

        $(function() {

            // Inicializar DataTable
            tabla = $("#tablaEmpleados").DataTable({
                pageLength: 10,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('netlan.admin.empleados.index') }}",
                    type: "GET"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nombres',
                        name: 'nombres',
                        render: function(data, type, row) {
                            let foto = row.foto ? window.baseUrl + row.foto : window.baseUrl +
                                'empleados/no-user.jpg';
                            return `
                        <div class="d-flex align-items-center">
                            <img src="${foto}" class="rounded-circle mr-2 border shadow-sm" style="width:40px; height:40px; object-fit: cover;">
                            <div>
                                <div class="font-weight-bold text-dark">${row.nombres} ${row.apellidos}</div>
                                <small class="text-muted"><i class="fas fa-envelope mr-1"></i>${row.usuario ? row.usuario.email : 'Sin correo'}</small>
                            </div>
                        </div>
                    `;
                        }
                    },
                    {
                        data: 'dni',
                        name: 'dni',
                        defaultContent: '<span class="text-muted">N/A</span>'
                    },
                    {
                        data: 'reloj',
                        name: 'reloj',
                        render: function(data) {
                            return data ?
                                `<span class="badge badge-light border"><i class="fas fa-fingerprint text-info mr-1"></i>${data}</span>` :
                                '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'departamento',
                        name: 'departamento.nombre',
                        defaultContent: '<span class="text-muted">Sin Asignar</span>'
                    },
                    {
                        data: 'puesto',
                        name: 'puesto',
                        defaultContent: '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        render: function(data) {
                            return data === 'Activo' ?
                                '<span class="badge badge-success px-2 py-1">Activo</span>' :
                                '<span class="badge badge-secondary px-2 py-1">Inactivo</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                        <button class="btn btn-sm btn-info btnEditar" data-id="${row.id}" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btnEliminar" data-id="${row.id}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                        }
                    }
                ]
            });

            // Escuchar la respuesta AJAX para actualizar las tarjetas estadísticas
            tabla.on('xhr.dt', function(e, settings, json, xhr) {
                if (json) {
                    $('#statTotal').text(json.totalCount ?? 0);
                    $('#statActivos').text(json.activosCount ?? 0);
                    $('#statInactivos').text(json.inactivosCount ?? 0);
                }
            });


// Abrir Modal para Crear
            $('#btnCrear').click(function() {
                limpiarFormulario();
                $('#modalTitle').html('<i class="fas fa-user-plus mr-2"></i>Nuevo Empleado');
                $('#modalEmpleado').modal('show');
            });

            // Submit Formulario con AJAX
            $('#formEmpleado').submit(function(e) {
                e.preventDefault();

                let id = $('#empleado_id').val();
                let url = id 
                    ? "{{ route('netlan.admin.empleados.update', ':id') }}".replace(':id', id) 
                    : "{{ route('netlan.admin.empleados.store') }}";

                let formData = new FormData(this);
                if (id) {
                    formData.append('_method', 'PUT');
                }

                // Estado de carga en botón
                setLoadingState(true);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modalEmpleado').modal('hide');
                        tabla.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: '¡Operación Exitosa!',
                            text: id ? 'El empleado ha sido actualizado correctamente.' : 'El empleado ha sido creado correctamente.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        limpiarErrores();

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('.error-' + key).text(value[0]);
                            });

                            // Ir a la primera pestaña que contenga errores para visibilidad
                            let $firstError = $('.is-invalid').first();
                            if ($firstError.length) {
                                let tabId = $firstError.closest('.tab-pane').attr('id');
                                $(`a[href="#${tabId}"]`).tab('show');
                            }

                            Swal.fire({
                                icon: 'warning',
                                title: 'Campos requeridos',
                                text: 'Por favor completa la información obligatoria marcada en rojo.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error del servidor',
                                text: 'No se pudo procesar la solicitud. Intenta nuevamente.'
                            });
                        }
                    },
                    complete: function() {
                        setLoadingState(false);
                    }
                });
            });

            // Previsualización Dinámica de Imágenes
            $('#foto').change(function() { previewImage(this, 'previewFoto'); });
            $('#fotoDni').change(function() { previewImage(this, 'previewDni'); });
            $('#fotoLic').change(function() { previewImage(this, 'previewLic'); });

            // Abrir Ficha
            $(document).on('click', '.ficha', function() {
                let id = $(this).data('id');
                window.open("{{ route('netlan.admin.empleados.ficha', ':id') }}".replace(':id', id), '_blank');
            });

            // Editar Empleado
            $(document).on('click', '.btnEditar', function() {
                limpiarFormulario();
                $('#modalTitle').html('<i class="fas fa-user-edit mr-2"></i>Editar Empleado');

                let id = $(this).data('id');

                $.ajax({
                    url: "{{ route('netlan.admin.empleados.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(data) {
                        let empleado = data.empleado || {};
                        let baseUrl = window.baseUrl;

                        $('#empleado_id').val(empleado.id);
                        $('#nombres').val(empleado.nombres);
                        $('#apellidos').val(empleado.apellidos);
                        $('#dni').val(empleado.dni);
                        $('#telefono').val(empleado.telefono);
                        $('#direccion').val(empleado.direccion);
                        $('#puesto').val(empleado.puesto);
                        $('#fecha_nacimiento').val(empleado.fecha_nacimiento);
                        $('#fecha_inicio').val(empleado.fecha_inicio);
                        $('#departamento_id').val(empleado.departamento_id);
                        $('#reloj').val(empleado.reloj);
                        $('#estado').val(empleado.estado);

                        if (empleado.usuario) {
                            $('#email').val(empleado.usuario.email);
                            $('#usuario_id').val(empleado.usuario.id);
                        }

                        // Cargar imágenes guardadas o placeholders
                        $('#previewFoto').attr('src', empleado.foto ? baseUrl + empleado.foto : baseUrl + 'empleados/no-user.jpg');
                        $('#previewDni').attr('src', empleado.fotoDni ? baseUrl + empleado.fotoDni : baseUrl + 'empleados/no_dni.jpg');
                        $('#previewLic').attr('src', empleado.fotoLic ? baseUrl + empleado.fotoLic : baseUrl + 'empleados/no_lic.png');

                        $('#modalEmpleado').modal('show');
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los datos del empleado.' });
                    }
                });
            });


            // Eliminar Empleado
            $(document).on('click', '.btnEliminar', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: '¿Confirmar eliminación?',
                    text: "Se desactivará/eliminará el registro del empleado.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('netlan.admin.empleados.destroy', ':id') }}"
                                .replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                tabla.ajax.reload(null, false);
                                Swal.fire('¡Eliminado!', 'El empleado fue eliminado.',
                                    'success');
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Ocurrió un error al intentar eliminar.',
                                    'error');
                            }
                        });
                    }

                });
            });
        });



        // Previsualizar Imagen Seleccionada
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + previewId).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Limpieza General del Formulario y Errores
        function limpiarFormulario() {
            $('#formEmpleado')[0].reset();
            $('#empleado_id').val('');
            $('#usuario_id').val('');
            limpiarErrores();
            $('#tab-personales').tab('show'); // Volver a la primera pestaña

            let baseUrl = window.baseUrl;
            $('#previewFoto').attr('src', baseUrl + 'empleados/no-user.jpg');
            $('#previewDni').attr('src', baseUrl + 'empleados/no_dni.jpg');
            $('#previewLic').attr('src', baseUrl + 'empleados/no_lic.png');
        }

        function limpiarErrores() {
            $('.form-control, .custom-select').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        function setLoadingState(loading) {
            if (loading) {
                $('#btnGuardar').prop('disabled', true);
                $('#btnGuardarText').text('Guardando...');
                $('#btnGuardar i').attr('class', 'fas fa-spinner fa-spin mr-1');
            } else {
                $('#btnGuardar').prop('disabled', false);
                $('#btnGuardarText').text('Guardar Empleado');
                $('#btnGuardar i').attr('class', 'fas fa-save mr-1');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {


            async function cargarEstadisticas() {
                // 1. Indicar visualmente el estado de carga (opcional)
                const spanTotal = document.getElementById('statTotal');
                if (spanTotal) {
                    spanTotal.innerHTML = '<i class="fas fa-spinner fa-spin text-muted h6 mb-0"></i>';
                }

                try {
                    // 2. Consulta al endpoint definido en el controlador
                    const response = await fetch("{{ route('netlan.admin.empleados.estadisticas') }}", {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }

                    const data = await response.json();
                    cosole.log(data);
                    // 3. Poblar los elementos HTML
                    actualizarValor('statTotal', data.total);
                    actualizarValor('statActivos', data.activos);
                    actualizarValor('statPendientes', data.pendientes);

                } catch (error) {
                    console.error('Error al cargar métricas de empleados:', error);
                    actualizarValor('statTotal', 0);
                    actualizarValor('statActivos', 0);
                    actualizarValor('statPendientes', 0);
                }
            }

            function actualizarValor(id, valor) {
                const el = document.getElementById(id);
                if (el) {
                    const numero = Number(valor) || 0;
                    el.innerText = numero.toLocaleString();
                }
            }
        });
    </script>
@stop
