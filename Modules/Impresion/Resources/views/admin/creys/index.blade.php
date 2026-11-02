@extends('adminlte::page')
@section('title', 'Dashboard')

@section('content_header')
    <h1>Administrar registros CREY</h1>
    <a href="{{ route('impresion.panel') }}" class="btn btn-primary">Ir al panel de impresión</a>
@stop
@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Importar excel CREY</h4>
        </div>
        <div class="card-body">

            <form action="{{ url('impresion/admin/creys/importar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file">Seleccionar archivo Excel (.xlsx)</label>
                    <input type="file" name="import_file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Importar datos</button>
            </form>

            {{-- Alertas de éxito o error --}}



        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Total alumnos Registrados</h3>

                </div> <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaImpresos" class="table table-bordered table-hover table-striped table-sm">
                            <thead>
                                <tr>
                                    {{-- <th>NCurso</th> --}}
                                    <th>Curso</th>
                                    <th>Sección</th>
                                    <th>Modalidad</th>
                                    <th>Pendientes</th>
                                    <th>Impresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estadisticas as $row)
                                    <tr>
                                        {{-- <td>{{ $row->orden_curso }}</td> --}}
                                        <td>{{ $row->curso }}</td>
                                        <td>{{ $row->seccion }}</td>
                                        <td>{{ $row->modalidad }}</td>
                                        <td class="text-danger fw-bold">{{ $row->pendientes }}</td>
                                        <td class="text-success fw-bold">{{ $row->completos }}</td>
                                    </tr>
                                @endforeach
                                <tr style="background:#f4f6f9;">
                                    <td colspan="3"><strong>TOTAL GENERAL</strong></td>
                                    <td class="text-danger fw-bold">
                                        {{ $totales->total_pendientes }}
                                    </td>
                                    <td class="text-success fw-bold">
                                        {{ $totales->total_completos }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">alumnos Registrados</h3>

                </div> <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablacrey" class="table table-bordered table-hover table-striped table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Modalidad</th>
                                    <th scope="col">Curso</th>
                                    <th scope="col">seccion</th>
                                    <th scope="col">DNI</th>
                                    <th scope="col">Nombres</th>
                                    <th scope="col">Apellidos</th>
                                    <th scope="col">Foto</th>

                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody> @php $contador = 1; @endphp @foreach ($creydatos as $item)
                                    <tr class="">
                                        <td scope="row">{{ $contador++ }}</td>
                                        <td>{{ $item->modalidad }}</td>
                                        <td>{{ $item->curso }}</td>
                                        <td>{{ $item->seccion }}</td>
                                        <td>{{ $item->dni }}</td>
                                        <td>{{ $item->nombres }}</td>
                                        <td>{{ $item->apellidos }}</td>
                                        @if ($item->observaciones == 'Pendiente')
                                            <td style="color: red">
                                                <strong>{{ $item->observaciones }}</strong>
                                            </td>
                                        @else
                                            <td>{{ $item->observaciones }}</td>
                                        @endif
                                        {{-- <td> <a href="javascript:void(0)" class="btn btn-sm toggle-estado {{ $equipo->estado == 'Activo' ? 'btn-success' : 'btn-danger' }}" data-id="{{ $equipo->id }}"> {{-- <i class="fas fa-desktop"></i> - --}} {{-- {{ $equipo->estado == 'Activo' ? 'Act' : 'InAct' }} </a> --}}
                                        {{-- </td> --}}
                                        <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <button class="btn btn-warning btn-sm btn-editar"
                                                    data-id="{{ $item->id }}" data-nombres="{{ $item->nombres }}"
                                                    data-apellidos="{{ $item->apellidos }}" data-dni="{{ $item->dni }}"
                                                    data-curso="{{ $item->curso }}" data-seccion="{{ $item->seccion }}"
                                                    data-modalidad="{{ $item->modalidad }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ url('impresion/admin/creys', $item->id) }}" method="POST"
                                                    class="form-eliminar">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="button" class="btn btn-danger btn-sm btn-eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
        </div>
    </div>
    </div> <!-- Modal -->
    <div class="modal fade" id="previewModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Vista previa de importación</h5>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="tablaPreview">
                        <thead>
                            <tr>
                                <th>DNI</th>
                                <th>Nombre</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <footer> <button class="btn btn-success" onclick="confirmarImportacion()"> Confirmar Importación
                        </button> </footer>
                </div>
            </div>
        </div>
    </div>

    {{-- modal editar --}}
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formEditar" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">Editar Registro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" id="edit_id">

                        <div class="mb-3">
                            <label class="form-label">Nombres</label>
                            <input type="text" name="nombres" id="edit_nombres" class="form-control" required>
                            <div class="invalid-feedback">Ingrese los nombres.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" id="edit_apellidos" class="form-control" required>
                            <div class="invalid-feedback">Ingrese los apellidos.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">DNI</label>
                            <input type="text" name="dni" id="edit_dni" class="form-control" required>
                            <div class="invalid-feedback">Ingrese el DNI.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Curso</label>
                            <input type="text" name="curso" id="edit_curso" class="form-control" required>
                            <div class="invalid-feedback">Ingrese el curso.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sección</label>
                            <input type="text" name="seccion" id="edit_seccion" class="form-control" required>
                            <div class="invalid-feedback">Ingrese la sección.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Modalidad</label>
                            <input type="text" name="modalidad" id="edit_modalidad" class="form-control" required>
                            <div class="invalid-feedback">Ingrese la modalidad.</div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Actualizar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')

    <script>
        $(function() {
            $("#tablacrey").DataTable({
                "pageLength": 10,
                "responsive": true,
                "order": [
                    [5, 'asc']
                ],
                "lengthChange": true,
                "autoWidth": false,

                buttons: [{
                        text: '<i class="fas fa-copy"></i> COPIAR',
                        extend: 'copy',
                        className: 'btn btn-default'
                    },
                    {
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        extend: 'pdf',
                        className: 'btn btn-danger'
                    },
                    {
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        extend: 'csv',
                        className: 'btn btn-info'
                    },
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
            }).buttons().container().appendTo('#tablacrey_wrapper .row:eq(0)');
        });
    </script>
    <script>
        $(function() {
            $("#tablaImpresos").DataTable({
                "pageLength": 10,
                "responsive": true,
                // "order": [
                //     [5, 'asc']
                // ],
                "lengthChange": true,
                "autoWidth": false,

                buttons: [{
                        text: '<i class="fas fa-copy"></i> COPIAR',
                        extend: 'copy',
                        className: 'btn btn-default'
                    },
                    {
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        extend: 'pdf',
                        className: 'btn btn-danger'
                    },
                    {
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        extend: 'csv',
                        className: 'btn btn-info'
                    },
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
            }).buttons().container().appendTo('#tablaImpresos_wrapper .row:eq(0)');
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.btn-eliminar').forEach(btn => {

                btn.addEventListener('click', function() {

                    let form = this.closest('form');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "No podrás revertir esto",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });

                });

            });

        });
    </script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
    <script>
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                background: '#fff',
                color: '#333',
                iconColor: '#e74c3c',
                confirmButtonColor: '#e74c3c',
                confirmButtonText: 'Entendido',
            });
        @endif
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modalEditar = $('#modalEditar');

            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    // Llenar campos
                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_nombres').value = this.dataset.nombres;
                    document.getElementById('edit_apellidos').value = this.dataset.apellidos;
                    document.getElementById('edit_dni').value = this.dataset.dni;
                    document.getElementById('edit_curso').value = this.dataset.curso;
                    document.getElementById('edit_seccion').value = this.dataset.seccion;
                    document.getElementById('edit_modalidad').value = this.dataset.modalidad;

                    // Actualizar action del form
                    let form = document.getElementById('formEditar');
                    form.action = "{{ route('impresion.admin.creys.update', ':id') }}".replace(
                        ':id', id);

                    // Abrir modal
                    modalEditar.modal('show');
                });
            });

            // Validación de bootstrap
            const form = document.getElementById('formEditar');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });

        });
    </script>
@endsection

