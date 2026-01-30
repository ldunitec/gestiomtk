@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-md-12">
            <h1><b>Listado de Asistencia </b></h1>
            <hr>
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalImportar">
                📂 Importar TXT
            </button>

            <button class="btn btn-warning mb-3" data-toggle="modal" data-target="#modalConfig">
                ⚙️ Configurar Marcación
            </button>


        </div>
    </div>



@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form method="GET" class="row mb-3">

                <div class="col-md-3">
                    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
                </div>

                <div class="col-md-3">
                    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>


        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="tablaAsistencias" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Tiempo tarde</th>
                            <th>Estado Entrada</th>
                            <th>Estado Salida</th>
                            <th>Horas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asistencias as $a)
                            <tr
                                class="
            {{ str_contains($a['estado_entrada'], 'Tarde') ? 'table-warning' : '' }}
            {{ str_contains($a['estado_entrada'], 'Sin') || str_contains($a['estado_salida'], 'Sin') ? 'table-danger' : '' }}
        ">
                                <td>{{ $a['empleado_id'] }}</td>
                                <td>
                                    {{ $a['empleado_nombre'] }}
                                    <br>
                                    <small class="text-muted">
                                        Código: {{ $a['empleado_id'] }}
                                    </small>
                                </td>

                                <td>{{ $a['fecha'] }}</td>
                                <td>{{ $a['entrada'] ?? '-' }}</td>
                                <td>{{ $a['salida'] ?? '-' }}</td>
                                <td class="{{ $a['tiempo_tarde'] > 0 ? 'text-danger font-weight-bold' : '' }}">
                                    {{ $a['tiempo_tarde'] > 0 ? $a['tiempo_tarde'] . ' min' : '—' }}
                                </td>
                                <td>{{ $a['estado_entrada'] }}</td>
                                <td>{{ $a['estado_salida'] }}</td>
                                <td>{{ $a['horas_trabajadas'] ?? '00:00' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>



                <button onclick="window.print()" class="btn btn-primary">
                    🖨️ Imprimir
                </button>


                <!-- /.card -->
            </div>
        </div>
    </div>


    </div>
    </div>

    <div class="modal fade" id="modalImportar" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('admin.marcaciones.importar') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title">Importar archivo TXT</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Seleccione archivo TXT</label>
                            <input type="file" name="archivo" class="form-control" accept=".txt" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Importar
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- Configuracion de horario --}}
    <div class="modal fade" id="modalConfig" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.marcaciones.configurar') }}">
                @csrf

                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Configuración de Asistencia</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <h6 class="text-primary">Rango de ENTRADA</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Desde</label>
                                <input type="time" name="entrada_desde" class="form-control"
                                    value="{{ $ciclo->entrada_desde }}">
                            </div>
                            <div class="col-md-6">
                                <label>Hasta</label>
                                <input type="time" name="entrada_hasta" class="form-control"
                                    value={{ $ciclo->entrada_hasta }}>
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-success">Rango de SALIDA</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Desde</label>
                                <input type="time" name="salida_desde" class="form-control"
                                    value="{{ $ciclo->salida_desde }}">
                            </div>
                            <div class="col-md-6">
                                <label>Hasta</label>
                                <input type="time" name="salida_hasta" class="form-control"
                                    value="{{ $ciclo->entrada_hasta }}">
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-danger">Tolerancia</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Hora oficial de entrada</label>
                                <input type="time" name="hora_oficial" class="form-control"
                                    value="{{ $ciclo->hora_oficial }}">
                            </div>
                            <div class="col-md-6">
                                <label>Hora oficial de salida</label>
                                <input type="time" name="hora_salida" class="form-control"
                                    value="{{ $ciclo->hora_salida }}">
                            </div>
                            <div class="col-md-6">
                                <label>Minutos permitidos</label>
                                <input type="number" name="minutos_permitidos" class="form-control"
                                    value="{{ $ciclo->minutos_permitidos }}">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">
                            Guardar configuración
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@stop

@section('css')
    @include('admin.marcas.parciales.css');
@stop

@section('js')
    {{-- @include('admin.marcas.parciales.js'); --}}
    <script>
        $(function() {
            $('#tablaAsistencias').DataTable({
                responsive: false,
                scrollX: true,
                autoWidth: false,
                order: [
                    [1, 'desc']
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                dom: 'Bfrtip',
                buttons: ['excel', 'pdf', 'print']
            });
        });
    </script>


@stop
