@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de empleados </b></h1>
    <hr>
@stop

@section('content')

    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">empleados registrados</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearEmpleadoModal">
                            Nuevo empleado
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-hover table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Nro</th>
                                    <th style="text-align: center">Departamento</th>
                                    <th style="text-align: center">Nombres</th>
                                    <th style="text-align: center">Apellidos</th>
                                    <th style="text-align: center">dni</th>
                                    <th style="text-align: center">Email</th>
                                    <th style="text-align: center">Fecha Nacimiento</th>
                                    <th style="text-align: center">Telefono</th>
                                    <th style="text-align: center">Reloj</th>
                                    <th style="text-align: center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach ($empleados as $empleado)
                                    <tr @class([
                                        'bg-danger text-white' => $empleado->estado === 'Inactivo',
                                    ])>
                                        <td style="text-align: center">{{ $contador++ }}</td>
                                        <td>{{ $empleado->departamento->nombre }}</td>
                                        <td>{{ $empleado->nombres }}</td>
                                        <td>{{ $empleado->apellidos }}</td>
                                        <td>{{ $empleado->dni }}</td>
                                        <td>{{ $empleado->usuario->email }}</td>
                                        <td>{{ $empleado->fecha_nacimiento }}</td>
                                        <td>{{ $empleado->telefono }}</td>
                                        <td>{{ $empleado->reloj }}</td>
                                        <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <a href="{{ url('/admin/empleados/' . $empleado->id . '/edit') }}"
                                                    class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                <form action="{{ url('/admin/empleados', $empleado->id) }}" method="post"
                                                    onclick="preguntar{{ $empleado->id }}(event)"
                                                    id="miFormulario{{ $empleado->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>


                                                @include('admin.empleados.parciales.modal');
                                                <!-- Modal edit -->
                                                {{-- <div class="modal fade" id="editEmpleadoModal" tabindex="-1" role="dialog"
                                                aria-labelledby="editEmpleadoModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <form action="{{ url('admin/empleados') . '/' . $empleado->id }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT');
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary">
                                                                <h5 class="modal-title" id="crearempleadoModalLabel">Agregar
                                                                    Nueva empleado</h5>
                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Cerrar">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="nombre">Nombre de la empleado</label>
                                                                    <input type="text" class="form-control"
                                                                        name="nombre"
                                                                        value="{{ old('nombres', $empleado->nombres) }}"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancelar</button>
                                                                    <button type="submit"
                                                                    class="btn btn-primary">Guardar</button>
                                                                </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div> --}}
                                                <script>
                                                    function preguntar{{ $empleado->id }}(event) {
                                                        event.preventDefault();
                                                        Swal.fire({
                                                            title: '¿Desea eliminar esta registro?',
                                                            text: '',
                                                            icon: 'question',
                                                            showDenyButton: true,
                                                            confirmButtonText: 'Eliminar',
                                                            confirmButtonColor: '#a5161d',
                                                            denyButtonColor: '#270a0a',
                                                            denyButtonText: 'Cancelar',
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                var form = $('#miFormulario{{ $empleado->id }}');
                                                                form.submit();
                                                            }
                                                        });
                                                    }
                                                </script>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>

    {{-- MODAL  --}}


    <!-- Modal create -->


    </div>
    </div>


@stop

@section('css')
    <style>
        /* Fondo transparente y sin borde en el contenedor */
        #example1_wrapper .dt-buttons {
            background-color: transparent;
            box-shadow: none;
            border: none;
            display: flex;
            justify-content: center;
            /* Centrar los botones */
            gap: 10px;
            /* Espaciado entre botones */
            margin-bottom: 15px;
            /* Separar botones de la tabla */
        }

        /* Estilo personalizado para los botones */
        #example1_wrapper .btn {
            color: #fff;
            /* Color del texto en blanco */
            border-radius: 4px;
            /* Bordes redondeados */
            padding: 5px 15px;
            /* Espaciado interno */
            font-size: 14px;
            /* Tamaño de fuente */
        }

        /* Colores por tipo de botón */
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-info {
            background-color: #17a2b8;
            border: none;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
            border: none;
        }

        .btn-default {
            background-color: #6e7176;
            color: #212529;
            border: none;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            $("#example1").DataTable({
                "pageLength": 10,
                "language": {
                    "emptyTable": "No hay información",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ empleados",
                    "infoEmpty": "Mostrando 0 a 0 de 0 empleados",
                    "infoFiltered": "(Filtrado de _MAX_ total empleados)",
                    "lengthMenu": "Mostrar _MENU_ empleados",
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
            }).buttons().container().appendTo('#example1_wrapper .row:eq(0)');
        });
    </script>
@stop
