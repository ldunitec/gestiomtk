@extends('adminlte::page')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Importar Alumnos</h4>
        </div>

        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.alumnos.importar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <input type="file" name="archivo" class="form-control" required>
                </div>

                <button class="btn btn-primary">
                    Importar Excel
                </button>

            </form>

        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">alumnos Registrados</h3>

                        <div class="card-tools">
                            <!-- Botón para abrir el modal -->
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#crearEquipoModal">
                                Nuevo equipo
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
                                        <th scope="col">#</th>
                                        <th scope="col">Modalidad</th>
                                        <th scope="col">Curso</th>
                                        <th scope="col">seccion</th>
                                        <th scope="col">Jornada</th>
                                        <th scope="col">DNI</th>
                                        <th scope="col">Nombres</th>
                                        <th scope="col">Apellidos</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $contador = 1;
                                    @endphp

                                    @foreach ($alumnos as $item)
                                        <tr class="">
                                            <td scope="row">{{ $contador++ }}</td>
                                            <td>{{ $item->modalidad }}</td>
                                            <td>{{ $item->curso }}</td>
                                            <td>{{ $item->seccion }}</td>
                                            <td>{{ $item->jornada }}</td>
                                            <td>{{ $item->dni }}</td>
                                            <td>{{ $item->nombres }}</td>
                                            <td>{{ $item->apellidos }}</td>
                                            {{-- <td>
                                                <a href="javascript:void(0)"
                                                    class="btn btn-sm toggle-estado {{ $equipo->estado == 'Activo' ? 'btn-success' : 'btn-danger' }}"
                                                    data-id="{{ $equipo->id }}">
                                                    {{-- <i class="fas fa-desktop"></i> - --}}
                                            {{-- {{ $equipo->estado == 'Activo' ? 'Act' : 'InAct' }}
                                                </a> --}}
                                            {{-- </td> --}}
                                            <td style="text-align: center">
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <a href="{{ url('/admin/alumnos/' . $item->id . '/edit') }}"
                                                        class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>


                                                    <form action="{{ url('/admin/alumnos', $item->id) }}" method="post"
                                                        onclick="preguntar{{ $item->id }}(event)"
                                                        id="miFormulario{{ $item->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>


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







    </div>
@endsection

@section('js')


       {{-- dataTable --}}
       <script>
           $(function() {
               $("#example1").DataTable({
                   "pageLength": 10,
                   "response": true,
                   "order": [
                       [5, 'asc']
                   ],
                   "language": {
                       "emptyTable": "No hay información",
                       "info": "Mostrando _START_ a _END_ de _TOTAL_ alumnos",
                       "infoEmpty": "Mostrando 0 a 0 de 0 alumnos",
                       "infoFiltered": "(Filtrado de _MAX_ total alumnos)",
                       "lengthMenu": "Mostrar _MENU_ alumnos",
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
                   "responsive": true,
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
@endsection