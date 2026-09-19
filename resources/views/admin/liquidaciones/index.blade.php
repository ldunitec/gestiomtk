@extends('adminlte::page')

@section('title', 'Historial de Liquidaciones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-history text-primary mr-2"></i>Historial de Liquidaciones</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalSolicitud">
            <i class="fas fa-plus mr-1"></i> Nueva Solicitud
        </button>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaLiquidaciones" class="table table-bordered table-striped table-hover w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Ciudad</th>
                            <th>Fecha</th>
                            <th>Asignado</th>
                            <th>Total</th>
                            <th>Depósito</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Detalle de Gastos --}}
    <div class="modal fade" id="modalGastos" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-receipt mr-1"></i> Detalle de Gastos</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detalleGastos"></div>
                <div class="modal-footer">
    <button type="button" class="btn btn-danger" id="btnImprimirPdfModal">
        <i class="fas fa-file-pdf mr-1"></i> Imprimir PDF
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
</div>
            </div>
        </div>
    </div>

    {{-- Modal Solicitud de Viáticos --}}
    <div class="modal fade" id="modalSolicitud" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formSolicitud">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-file-invoice mr-1"></i> Solicitud de Viáticos</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="solicitud_id" value="{{ $solicitud->id ?? '' }}">

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="ciudad">Ciudad Destino</label>
                                <input type="text" name="ciudad" id="ciudad" class="form-control"
                                    value="{{ $solicitud->ciudad ?? '' }}" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="fecha_solicitud">Fecha Solicitud</label>
                                <input type="date" name="fecha_solicitud" id="fecha_solicitud" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="asignado">Monto Asignado</label>
                                <input type="number" step="0.01" name="asignado" id="asignado" class="form-control"
                                    value="{{ $solicitud->monto_total ?? '' }}" placeholder="0.00" required>
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="fecha_ida">Fecha Ida</label>
                                <input type="date" name="fecha_ida" id="fecha_ida" class="form-control" required>
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="fecha_regreso">Fecha Regreso</label>
                                <input type="date" name="fecha_regreso" id="fecha_regreso" class="form-control" required>
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold text-secondary mb-3"><i class="fas fa-coins mr-1"></i> Desglose Estimado
                        </h6>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Alimentación</label>
                                <input type="number" step="0.01" name="alimentacion" placeholder="0.00"
                                    class="form-control">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Hospedaje</label>
                                <input type="number" step="0.01" name="hospedaje" placeholder="0.00"
                                    class="form-control">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Transporte</label>
                                <input type="number" step="0.01" name="transporte" placeholder="0.00"
                                    class="form-control">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Combustible</label>
                                <input type="number" step="0.01" name="combustible" placeholder="0.00"
                                    class="form-control">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Peajes</label>
                                <input type="number" step="0.01" name="peajes" placeholder="0.00"
                                    class="form-control">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Extra / Varios</label>
                                <input type="number" step="0.01" name="extra" placeholder="0.00"
                                    class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Guardar
                            Solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        
        let tabla;

        $(document).ready(function() {
            // Inicialización de DataTables
            tabla = $('#tablaLiquidaciones').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.liquidaciones.data') }}',

                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'ciudad',
                        name: 'ciudad'
                    },
                    {
                        data: 'fecha_entrega',
                        name: 'fecha_entrega'
                    },
                    {
                        data: 'asignado',
                        name: 'asignado'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'deposito',
                        name: 'deposito'
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],


                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                }
            });

            // Acción: Entregar Liquidación (Cambia a Estado 2)
            $('#tablaLiquidaciones').on('click', '.entregar', function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.liquidaciones.entregar', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: '¿Marcar como Entregado?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Sí, entregar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                                _token: '{{ csrf_token() }}'
                            })
                            .done(function(response) {
                                Swal.fire('¡Actualizado!', response.message, 'success');
                                // Forzar el recargado dinámico de la tabla sin reiniciar la paginación
                                tabla.ajax.reload(null, false);
                            })
                            .fail(function(xhr) {
                                Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
                            });
                    }
                });
            });

            // Acción: Cerrar Liquidación
            $('#tablaLiquidaciones').on('click', '.cerrar', function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.liquidaciones.liquidar', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: '¿Cerrar liquidación?',
                    text: 'Una vez cerrada no se podrán hacer modificaciones directas.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, entregar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                                _token: '{{ csrf_token() }}'
                            })
                            .done(function() {
                                Swal.fire('Cerrado', 'La liquidación se entregó correctamente',
                                    'success');
                                tabla.ajax.reload(null, false);
                            })
                            .fail(function(xhr) {
                                let mensaje = xhr.responseJSON && xhr.responseJSON.message ? xhr
                                    .responseJSON.message : 'No se pudo cerrar la liquidación';
                                Swal.fire('Error', mensaje, 'error');
                            });
                    }
                });
            });

            // Acción: Reabrir Liquidación
            $('#tablaLiquidaciones').on('click', '.abrir', function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.liquidaciones.reabrir', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: '¿Reabrir liquidación?',
                    text: 'La liquidación volverá a estado Sin Entregar.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, reabrir',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                                _token: '{{ csrf_token() }}'
                            })
                            .done(function(response) {
                                Swal.fire('Abierto', response.message, 'success');
                                // Forzar el recargado dinámico de la tabla
                                tabla.ajax.reload(null, false);
                            })
                            .fail(function(xhr) {
                                Swal.fire('Error', 'No se pudo reabrir la liquidación',
                                    'error');
                            });
                    }
                });
            });
            // Acción: Ver Detalle de Gastos
            $('#tablaLiquidaciones').on('click', '.ver', function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.liquidaciones.show', ':id') }}".replace(':id', id);

                $('#modalGastos').modal('show');
                $('#detalleGastos').html(`
                    <div class="text-center my-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Cargando detalles...</p>
                    </div>
                `);

                $.get(url)
                    .done(function(data) {
                        let badgeEstado = parseInt(data.estado) === 2 ?
                            '<span class="badge badge-success">' +
                            '<i class="fas fa-check-circle mr-1"></i>Entregado</span>' :
                            '<span class="badge badge-warning">' +
                            '<i class="fas fa-clock mr-1"></i>Sin Entregar</span>';
                        let html = `
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Ciudad:</strong> ${data.ciudad ?? '-'}</div>
                                <div class="col-md-4"><strong>Fecha:</strong> ${data.fecha_entrega ?? '-'}</div>
                                <div class="col-md-4"><strong>Estado:</strong> ${badgeEstado}</div>
                            </div>
                            <hr>
                        `;

                        html += `
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Ciudad</th>
                                            <th class="text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        let totalGastos = 0;

                        if (data.gastos && data.gastos.length > 0) {
                            data.gastos.forEach(g => {
                                let monto = parseFloat(g.monto) || 0;
                                totalGastos += monto;
                                html += `
                                    <tr>
                                        <td>${g.fecha ?? '-'}</td>
                                        <td><span class="badge badge-secondary">${g.tipo ?? '-'}</span></td>
                                        <td>${g.ciudad ?? '-'}</td>
                                        <td class="text-right">L. ${monto.toFixed(2)}</td>
                                    </tr>
                                `;
                            });

                            html += `
                                <tr class="table-active font-weight-bold">
                                    <td colspan="3" class="text-right">Total Acumulado:</td>
                                    <td class="text-right text-primary">L. ${totalGastos.toFixed(2)}</td>
                                </tr>
                            `;
                        } else {
                            html +=
                                `<tr><td colspan="4" class="text-center text-muted">No hay gastos detallados registrados.</td></tr>`;
                        }

                        html += `</tbody></table></div>`;
                        $('#detalleGastos').html(html);
                    })
                    .fail(function() {
                        $('#detalleGastos').html(
                            '<div class="alert alert-danger">Error al obtener el detalle de la liquidación.</div>'
                        );
                    });
            });
       
        });
    </script>
@endsection
