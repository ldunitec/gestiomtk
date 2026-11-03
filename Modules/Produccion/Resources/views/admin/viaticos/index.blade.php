@extends('adminlte::page')

@section('title', 'Listado de Viáticos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">
                <i class="fas fa-file-invoice-dollar text-primary mr-2"></i>Gestión de Viáticos
            </h1>
            <p class="text-muted mb-0">Producción · Viáticos y mano de obra.</p>
        </div>
        <a href="{{ route('admin.viaticos.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle mr-1"></i> Nuevo Viático
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-list mr-1"></i> Registros de Viáticos
                </h3>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaViaticos" class="table table-striped table-hover align-middle w-100">
                        <thead class="thead-light">
                            <tr>
                                <th>Ciudad</th>
                                <th>Origen</th>
                                <th class="text-center">Distancia</th>
                                <th class="text-right">Hospedaje</th>
                                <th class="text-right">Combustible</th>
                                <th class="text-right">Alimentación</th>
                                <th class="text-right">Extra</th>
                                <th class="text-right">Mano de obra</th>
                                <th class="text-right">Subtotal</th>
                                <th class="text-right">Impuesto</th>
                                <th class="text-right">Total a pagar</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Carga AJAX por DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- DataTables Bootstrap 4 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
@stop

@section('js')
    {{-- DataTables Scripts --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tablaViaticos').DataTable({
                ajax: "{{ route('viaticos.list') }}",
                responsive: true,
                autoWidth: false,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                columns: [{
                        data: 'ciudad',
                        render: function(data) {
                            return $('<span>').text(data).html();
                        }
                    },
                    {
                        data: 'origen',
                        defaultContent: 'No registrado',
                        render: $.fn.dataTable.render.text()
                    },
                    {
                        data: 'distancia_km',
                        className: 'text-center',
                        render: function(data) {
                            return `<span class="badge badge-light border"><i class="fas fa-route text-muted mr-1"></i>${parseFloat(data).toLocaleString()} km</span>`;
                        }
                    },
                    {
                        data: 'hospedaje',
                        className: 'text-right font-weight-bold',
                        render: d => 'L. ' + parseFloat(d).toFixed(2)
                    },
                    {
                        data: 'combustible',
                        className: 'text-right font-weight-bold',
                        render: d => 'L. ' + parseFloat(d).toFixed(2)
                    },
                    {
                        data: 'alimentacion',
                        className: 'text-right font-weight-bold',
                        render: d => 'L. ' + parseFloat(d).toFixed(2)
                    },
                    {
                        data: 'extra',
                        className: 'text-right font-weight-bold',
                        render: d => 'L. ' + parseFloat(d).toFixed(2)
                    },
                    {
                        data: 'mano_obra',
                        defaultContent: 0,
                        className: 'text-right',
                        render: d => 'L. ' + Number(d || 0).toFixed(2)
                    },
                    {
                        data: 'subtotal',
                        className: 'text-right',
                        render: (d, type, row) => 'L. ' + Number(d ?? row.total).toFixed(2)
                    },
                    {
                        data: 'impuesto',
                        className: 'text-right',
                        render: (d, type, row) => d === null ? 'No registrado' : 'L. ' + Number(d).toFixed(2) + ' (' + Number(row.impuesto_porcentaje).toFixed(0) + '%)'
                    },
                    {
                        data: 'total',
                        className: 'text-right font-weight-bold text-success text-md',
                        render: d => 'L. ' + parseFloat(d).toFixed(2)
                    },

                    {
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        render: function(id) {
                            // Generamos la base del URL respetando el subdominio o carpeta (/public) donde esté alojada la app
                            var urlShow = @json(route('admin.viaticos.show', ['id' => '__ID__'])) .replace('__ID__', id);
                            var urlPdf = @json(route('admin.viaticos.pdf', ['id' => '__ID__'])) .replace('__ID__', id);

                            return `
            <div class="btn-group btn-group-sm">
                <a href="${urlShow}" class="btn btn-info" data-toggle="tooltip" title="Ver Detalle">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="${urlPdf}" target="_blank" class="btn btn-danger" data-toggle="tooltip" title="Descargar PDF">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        `;
                        }
                    }
                ]
            });
        });
    </script>
@stop
