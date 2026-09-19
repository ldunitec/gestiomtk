@extends('adminlte::page')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h4>Importar Alumnos Inst. Cristo Rey</h4>
        </div>

        <div class="card-tools">

            <button id="btnEliminarTodos" class="btn btn-danger">
                <i class="fas fa-trash"></i> Eliminar Todos
            </button>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('admin.alumnos.importar.crey') }}" method="POST" enctype="multipart/form-data">

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
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h4>Importar Alumnos Inst. Cristo Rey</h4>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $crey }}</h3>
                            <p>Total</p>
                        </div>

                        <div class="icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">

                @foreach ($cursos as $curso)
                    <div class="col-lg-2 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $curso->total }}</h3>
                                <p>{{ $curso->curso }}</p>
                            </div>

                            <div class="icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="alumnoTable" class="table table-bordered table-hover table-striped table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>

                                    <th scope="col">Nombres</th>
                                    <th scope="col">Apellidos</th>
                                    <th scope="col">DNI</th>
                                    <th scope="col">Curso</th>
                                    <th scope="col">seccion</th>
                                    <th scope="col">Modalidad</th>
                                    
                                    <th scope="col">CodigoFoto</th>
                                    <th scope="col">Foto</th>

                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach ($creys as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nombres }}</td>
                                        <td>{{ $item->apellidos }}</td>
                                        <td>{{ $item->dni }}</td>
                                        <td>{{ $item->curso }}</td>
                                        <td>{{ $item->seccion }}</td>
                                        <td>{{ $item->modalidad }}</td>
                                        <td>{{ $item->observaciones }}</td>
                                        <td>
                                            <img src="{{ asset('storage/carnetizacion/crey/' . $item->observaciones . '.jpg') }}" alt="" width="50px">
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


        </div>

    </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
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
                    fetch("{{ route('admin.alumnos.crey.eliminarTodos') }}", {
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

    {{-- 
    <script>
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
    </script>

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
    </script> --}}
@endsection
