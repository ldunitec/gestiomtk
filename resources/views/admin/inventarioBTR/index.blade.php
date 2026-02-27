@extends('adminlte::page')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Levantamiento de Inventario</h1>
        <button class="btn btn-primary" id="btnNuevo" data-toggle="modal" data-target="#modalInventario">
            <i class="fas fa-plus"></i> Nuevo Registro
        </button>
        <a type="button" href="{{ route('inventarioBTR.pdf_todos') }}" class="btn btn-danger btn-sm" target="_blank">
            📄 T-PDF
        </a>

    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="invent_table" class="table table-bordered table-hover table-striped table-sm table-responsive">
        <thead>
            <tr>
                <th>Localidad</th>
                <th>IP</th>
                <th>RC-308</th>
                <th>Fases</th>
                <th>Fibra</th>
                <th>Ping</th>
                <th>Camaras</th>
                <th>Fuente</th>
                <th>Switch</th>
                <th>X-COM</th>
                <th>SG-116</th>
                <th>Imagen</th>

                {{-- <th>Observ</th> --}}
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventarios as $inv)
                <tr>
                    <td>{{ $inv->localidad }}</td>
                    <td>{{ $inv->ip }}</td>
                    <td>{{ $inv->rc }}</td>
                    <td>{{ $inv->fases }}</td>
                    <td>{{ $inv->fibra }}</td>
                    <td>{{ $inv->ping }}</td>
                    <td>{{ $inv->camaras }}</td>
                    <td>{{ $inv->fuente }}</td>
                    <td>{{ $inv->switchPoE }}</td>
                    <td>{{ $inv->xcom }}</td>
                    <td>{{ $inv->sg116 }}</td>
                    <td>
                        @if ($inv->imagen)
                            <img src="{{ asset('storage/' . $inv->imagen) }}" alt="Imagen"
                                style="width:50px; height:30px; object-fit:cover; border-radius:5px;">
                        @else
                            <span>No hay imagen</span>
                        @endif
                    </td> {{-- <td>{{ $inv->observaciones }}</td> --}}
                    <td>
                        <button class="btn btn-sm btn-warning btnEditar" data-id="{{ $inv->id }}"
                            data-json='@json($inv)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="{{ route('inventarioBTR.pdf', $inv->id) }}" class="btn btn-danger btn-sm" target="_blank">
                            📄 PDF
                        </a>


                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @include('admin.inventarioBTR.modal')


@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/editor-senales.css') }}">
@stop

@section('js')


    {{-- dataTable --}}
    <script>
        $(function() {
            $("#invent_table").DataTable({
                "dom": 'Blfrtip',
                "pageLength": 10,
                "language": {
                    "emptyTable": "No hay información",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_",
                    "infoEmpty": "Mostrando 0 a 0 de 0",
                    "infoFiltered": "(Filtrado de _MAX_ total)",
                    "lengthMenu": "Mostrar _MENU_",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscador:",
                    "zeroRecords": "Sin resultados encontrados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                "responsive": false,
                "scrollX": true,
                "lengthChange": true,
                "autoWidth": false,
                buttons: [
                    {
                        text: '<i class="fas fa-file-excel"></i> EXCEL',
                        extend: 'excel',
                        className: 'btn btn-success'
                    },
                    {
                        text: '<i class="fas fa-print"></i> IMPRIMIR',
                        extend: 'print',
                        className: 'btn btn-warning'
                    }
                ]
            }).buttons().container().appendTo('#invent_table_wrapper .row:eq(0)');
        });
    </script>


    <script>
        $(function() {

            // 🔹 Cambiar IP al seleccionar localidad
            $('#localidad').on('change', function() {
                $('#ip').val($(this).find(':selected').data('ip') || '');
            });

            // 🔹 CREAR NUEVO
            $('#btnNuevo').click(function() {
                $('#formInventario')[0].reset();
                $('#previewImagen').hide().attr('src', '');
                $('#id').val('');
                $('#method').val('POST');
                $('#formInventario').attr('action', '/admin/inventarioBTR');
                $('#modalInventario').modal('show');
            });

            // 🔹 EDITAR
            $(document).on('click', '.btnEditar', function() {
                let data = $(this).data('json');

                $('#formInventario')[0].reset();
                $('#previewImagen').hide().attr('src', '');
                $('#modalInventario').modal('show');
                $('#formInventario').attr('action', '/admin/inventarioBTR/' + data.id);
                $('#method').val('PUT');

                $('#id').val(data.id);
                $('#localidad').val(data.localidad).change();
                $('#ip').val(data.ip);
                $('#rc').val(data.rc);
                $('#fases').val(data.fases);

                $('input[name="fibra"][value="' + data.fibra + '"]').prop('checked', true);
                $('input[name="ping"][value="' + data.ping + '"]').prop('checked', true);
                $('input[name="fuente"][value="' + data.fuente + '"]').prop('checked', true);
                $('input[name="switchPoE"][value="' + data.switchPoE + '"]').prop('checked', true);
                $('input[name="xcom"][value="' + data.xcom + '"]').prop('checked', true);
                $('input[name="sg116"][value="' + data.sg116 + '"]').prop('checked', true);

                $('#camaras').val(data.camaras);
                $('#fuente').val(data.fuente);
                $('#switchPoE').val(data.switchPoE);
                $('#xcom').val(data.xcom);
                $('#sg116').val(data.sg116);
                $('#observaciones').val(data.observaciones);

                if (data.imagen) {
                    $('#previewImagen').attr('src', '/storage/' + data.imagen).show();
                }
            });

            // 🔹 Preview de imagen al seleccionar archivo
            $(document).on('change', '#imagen', function() {
                const file = this.files[0];
                if (!file) {
                    $('#previewImagen').hide().attr('src', '');
                    return;
                }
                if (!file.type.startsWith('image/')) {
                    alert('El archivo seleccionado no es una imagen');
                    $(this).val('');
                    $('#previewImagen').hide().attr('src', '');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImagen').attr('src', e.target.result).fadeIn(200);
                };
                reader.readAsDataURL(file);
            });

            // 🔹 Limpiar modal al cerrar
            $('#modalInventario').on('hidden.bs.modal', function() {
                $('#formInventario')[0].reset();
                $('#previewImagen').hide().attr('src', '');
                $('#imagen').val('');
            });

            // 🔹 GUARDAR FORMULARIO
            // $('#btnGuardar').click(function(e) {
            //     e.preventDefault();

            //     // Exportar lienzo si necesitas generar imagen
            //     if (typeof exportarLienzo === 'function') {
            //         exportarLienzo();
            //     }

            //     let form = $('#formInventario')[0];
            //     let formData = new FormData(form);
            //     let url = $(form).attr('action');
            //     let method = $('#method').val(); // POST o PUT

            //     $.ajax({
            //         url: url,
            //         type: method,
            //         data: formData,
            //         contentType: false,
            //         processData: false,
            //         success: function(response) {
            //             $('#modalInventario').modal('hide');
            //             $('#formInventario')[0].reset();
            //             $('#previewImagen').hide().attr('src', '');
            //             // Recargar DataTable si existe
            //             if (typeof tabla !== 'undefined') {
            //                 tabla.ajax.reload(null, false);
            //             }
            //             Swal.fire({
            //                 icon: 'success',
            //                 title: 'Guardado correctamente'
            //             });
            //         },
            //         error: function(xhr) {
            //             let errors = xhr.responseJSON?.errors || {};
            //             let msg = '';
            //             for (let key in errors) {
            //                 msg += errors[key].join('<br>') + '<br>';
            //             }
            //             Swal.fire({
            //                 icon: 'error',
            //                 title: 'Error al guardar',
            //                 html: msg || 'Ocurrió un error inesperado'
            //             });
            //         }
            //     });
            // });

            $('#btnGuardar').click(function(e) {
                e.preventDefault();

                let form = $('#formInventario')[0];
                let formData = new FormData(form);

                let url = $(form).attr('action');
                let method = $('#method').val(); // POST | PUT

                formData.append('_method', method); // 🔥 CLAVE

                $.ajax({
                    url: url,
                    type: 'POST', // 🔥 SIEMPRE POST
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function() {
                        $('#modalInventario').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Guardado correctamente'
                        });

                        if (typeof tabla !== 'undefined') {
                            tabla.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Error al guardar'
                        });
                    }
                });
            });
            a

        });
    </script>



@stop
