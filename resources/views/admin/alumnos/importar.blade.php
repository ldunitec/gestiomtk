@extends('adminlte::page')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h4>Importar Alumnos</h4>
            <div class="card-tools">

                <button id="btnEliminarTodos" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Eliminar Todos
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('admin.alumnos.importar.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="file" name="archivos[]" multiple class="form-control">
                        </div>
                        <button class="btn btn-primary">
                            Importar Excel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr>


    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Alumnos Registrados: </h3>
                    <h2> {{ $total }}</h2>

                    <!-- /.card-tools -->
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"
                                colapsed></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-md-6"> <canvas id="grafica"></canvas></div> --}}
                        <div class="col-md-12">
                            {{-- @php
                                $niveles = [
                                    ['nombre' => 'Kinder', 'valor' => $kinder],
                                    ['nombre' => 'Preparatoria', 'valor' => $preparatoria],
                                    ['nombre' => 'Primero', 'valor' => $primero],
                                    ['nombre' => 'Segundo', 'valor' => $segundo],
                                    ['nombre' => 'Tercero', 'valor' => $tercero],
                                    ['nombre' => 'Cuarto', 'valor' => $cuarto],
                                    ['nombre' => 'Quinto', 'valor' => $quinto],
                                    ['nombre' => 'Sexto', 'valor' => $sexto],
                                    ['nombre' => 'Séptimo', 'valor' => $septimo],
                                    ['nombre' => 'Octavo', 'valor' => $octavo],
                                    ['nombre' => 'Noveno', 'valor' => $noveno],
                                ];
                            @endphp --}}

                            <div class="row mb-3">
                                @foreach ($bilingue as $curso => $total)
                                    <div class="col-md-2">
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $total }}</h3>
                                                <p>{{ $curso }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- @php
                                $secciones = [
                                    ['nombre' => 'Septimo 1', 'valor' => $septimo1],
                                    ['nombre' => 'Septimo 2', 'valor' => $septimo2],
                                    ['nombre' => 'Octavo 1', 'valor' => $octavo1],
                                    ['nombre' => 'Octavo 2', 'valor' => $octavo2],
                                    ['nombre' => 'Noveno 1', 'valor' => $noveno1],
                                    ['nombre' => 'Noveno 2', 'valor' => $noveno2],
                                    ['nombre' => 'Decimo E.Bil', 'valor' => $decimoEB],
                                    ['nombre' => 'Decimo Huma', 'valor' => $decimoHH],
                                    ['nombre' => 'Decimo Inform', 'valor' => $decimoINF],
                                    ['nombre' => 'Undecimo E.Bil', 'valor' => $undecimoEB],
                                    ['nombre' => 'Undecimo Huma', 'valor' => $undecimoHH],
                                    ['nombre' => 'Undecimo Inform', 'valor' => $undecimoINF],
                                    ['nombre' => 'Duodecimo E.Bil', 'valor' => $duodecimoEB],
                                    ['nombre' => 'Duodecimo Inform', 'valor' => $duodecimoINF],
                                ];
                            @endphp --}}

                            <div class="row mb-3">
                                @foreach ($gradosEscuela as $grado => $total)
                                    <div class="col-md-2">
                                        <div class="small-box bg-warning">
                                            <div class="inner">
                                                <h3>{{ $total }}</h3>
                                                <p>{{ $grado }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>


                            {{-- isemed  --}}
                            {{-- @php
                                $secciones = [
                                    ['nombre' => 'Septimo 1', 'valor' => $septimoIsemed],
                                    ['nombre' => 'octavo 1', 'valor' => $octavoIsemed],
                                    ['nombre' => 'noveno 1', 'valor' => $novenoIsemed],
                                    ['nombre' => 'Decimo Inform', 'valor' => $decimoINFIsemed],
                                    ['nombre' => 'Decimo Huma', 'valor' => $decimoHHIsemed],
                                    ['nombre' => 'Undecimo Inform', 'valor' => $undecimoINFIsemed],
                                    ['nombre' => 'Undecimo Huma', 'valor' => $undecimoHHIsemed],
                                ];
                            @endphp --}}

                            {{-- <div class="row mb-3">
                                @foreach ($modalidadesEscuela as $curso => $data)
                                    <div class="col-md-2">
                                        <div class="small-box bg-secondary">
                                            <div class="inner">
                                                <h3>{{ $curso }}</h3>
                                                <small>INF: {{ $data->informatica ?? 0 }}</small><br>
                                                <small>BCH: {{ $data->bachillerato ?? 0 }}</small><br>
                                                <small>EB: {{ $data->ejecutivo ?? 0 }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div> --}}

                            <div class="row">



                                <div class="row mb-3">
                                    @foreach ($isemed as $grado => $total)
                                        <div class="col-md-2">
                                            <div class="small-box bg-success">
                                                <div class="inner">
                                                    <h3>{{ $total }}</h3>
                                                    <p>{{ $grado }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>


                        </div>

                        {{-- /////////////////////////// --}}

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="row mb-3">

                <div class="col-md-3">
                    <label>Curso</label>
                    <select id="filtroCurso" class="form-control">
                        <option value="">Todos</option>
                        @foreach ($cursos as $curso)
                            <option value="{{ $curso }}">{{ $curso }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Sección</label>
                    <select id="filtroSeccion" class="form-control">
                        <option value="1">1</option>
                        <option value="2">2</option>

                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button id="btnPdf" class="btn btn-danger w-100">
                        <i class="fa fa-file-pdf"></i> Generar PDF
                    </button>
                </div>

            </div>

            <div class="table-responsive">
                <table id="alumnoTable" class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                        <tr>
                            <th scope="col">#</th>

                            <th scope="col">Institucion</th>
                            <th scope="col">Nombres</th>
                            <th scope="col">Apellidos</th>
                            <th scope="col">DNI</th>
                            <th scope="col">Curso</th>
                            <th scope="col">seccion</th>
                            <th scope="col">Modalidad</th>
                            <th scope="col">Foto</th>

                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alumnos as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->institucion }}</td>
                                <td>{{ $item->nombres }}</td>
                                <td>{{ $item->apellidos }}</td>
                                <td>{{ $item->dni }}</td>
                                <td>{{ $item->curso }}</td>
                                <td>{{ $item->seccion }}</td>
                                <td>{{ $item->modalidad }}</td>
                                @if ($item->observacion === 'Pendiente')
                                    <td style="color: red">{{ $item->observacion }}</td>
                                @else
                                    <td>{{ $item->observacion }}
                                @endif
                                </td>
                               

                                <td class="text-center">
                                    <div class="btn-group">

                                        <a href="{{ url('/admin/alumnos/' . $item->id . '/edit') }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>

                                        <form action="{{ url('/admin/alumnos', $item->id) }}" method="POST"
                                            onsubmit="preguntar{{ $item->id }}(event)">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger btn-sm">
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
        </div>
    </div>

    <!-- Modal -->
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
                    <footer>
                        <button class="btn btn-success" onclick="confirmarImportacion()">
                            Confirmar Importación
                        </button>
                    </footer>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script> --}}
    {{-- dataTable --}}
    <script>
        $(function() {
            $("#alumnoTable").DataTable({
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
                // dom: 'Bfrtip',
                buttons: [

                    {
                        extend: 'pdf',
                        text: 'PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Reporte de Alumnos',
                        className: 'btn btn-danger'
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
            }).buttons().container().appendTo('#alumnoTable_wrapper .row:eq(0)');
        });
    </script>
    <script>
        document.getElementById('btnPdf').addEventListener('click', function() {

            let curso = document.getElementById('filtroCurso').value;
            let seccion = document.getElementById('filtroSeccion').value;

            let baseUrl = "{{ route('admin.alumnos.pdfCursos') }}";

            let url = `${baseUrl}?curso=${encodeURIComponent(curso)}&seccion=${encodeURIComponent(seccion)}`;
            window.open(url, '_blank');
        });
    </script>

    {{-- <script>
        const data = @json($dBilingue);

        const labels = data.map(item => item.grado);
        const valores = data.map(item => item.total);

        new Chart(document.getElementById('grafica'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Alumnos',
                    data: valores,
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels], // activar plugin
            options: {
                indexAxis: 'y', // gráfica horizontal
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'right',
                        color: 'black',
                        font: {
                            weight: 'bold'
                        },
                        formatter: function(value) {
                            return value; // muestra el total
                        }
                    }
                }
            }
        });
    </script> --}}

    <script>
        .then(response => {
            window.registrosTemp = response.data; // guardar temporal
            mostrarPreview(response.data);
        });




        function mostrarPreview(data) {

            let tbody = document.querySelector("#tablaPreview tbody");
            tbody.innerHTML = '';
            ////////////////
            // 
            data.forEach(row => {
                let hayDuplicados = data.some(r => r.duplicado);

                if (hayDuplicados) {
                    document.querySelector("button.btn-success").disabled = true;
                    Swal.fire('Hay DNIs duplicados', 'Corrija antes de importar', 'error');
                } else {
                    document.querySelector("button.btn-success").disabled = false;
                }
                let filaClass = row.duplicado ? 'table-danger' : '';

                tbody.innerHTML += `
        <tr class="${filaClass}">
            <td>${row.dni}</td>
            <td>${row.nombre}</td>
            <td>${row.observacion}</td>
        </tr>
    `;



                ///////////

                // data.forEach(row => {

                //     let dniClass = row.dni ? '' : 'table-danger';
                //     let nombreClass = row.nombre ? '' : 'table-danger';
                //     let obsClass = row.observacion ? '' : 'table-danger';

                tbody.innerHTML += `
            <tr>
                <td class="${dniClass}">${row.dni ?? 'FALTANTE'}</td>
                <td class="${nombreClass}">${row.nombre ?? 'FALTANTE'}</td>
                <td class="${obsClass}">${row.observacion ?? 'FALTANTE'}</td>
            </tr>
        `;
            });

            $('#previewModal').modal('show');
        }

        function confirmarImportacion() {

            fetch("{{ route('admin.alumnos.guardar') }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        alumnos: window.registrosTemp
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert("Importación completada correctamente");
                    location.reload();
                });
        }
    </script>

    <script>
        document.getElementById('btnEliminarTodos').addEventListener('click', function() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Se eliminarán todos los registros!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar todo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('admin.alumnos.eliminarTodos') }}", {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Eliminado!', data.success, 'success');
                                location.reload();
                                // opcional: recargar tabla DataTables
                                $('#tablaAlumnos').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error', data.error, 'error');
                            }
                        })
                        .catch(() => Swal.fire('Error', 'No se pudo eliminar.', 'error'));
                }
            })
        });
    </script>
@endsection
