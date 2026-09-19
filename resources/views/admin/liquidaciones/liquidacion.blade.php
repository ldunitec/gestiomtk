@extends('adminlte::page')

@section('title', 'Liquidaciones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-file-invoice-dollar text-primary mr-2"></i>Registro de Facturas de Gastos de Viaje</h1>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="imprimir()">
            <i class="fas fa-print mr-1"></i> Imprimir
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Formulario de Encabezado / Resumen -->
    <div class="col-12">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Información General</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="visita"><i class="fas fa-map-marker-alt text-muted mr-1"></i> Ciudad Destino</label>
                        <input type="text" id="visita" class="form-control font-weight-bold" placeholder="Ej. San Pedro Sula">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="fecha_visita"><i class="fas fa-calendar-alt text-muted mr-1"></i> Fecha de Entrega</label>
                        <input type="date" id="fecha_visita" class="form-control font-weight-bold text-primary" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="Asignado"><i class="fas fa-wallet text-muted mr-1"></i> Asignado</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">L.</span></div>
                            <input type="number" step="0.01" id="Asignado" class="form-control font-weight-bold text-success" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="totalGeneral"><i class="fas fa-calculator text-muted mr-1"></i> Total Gastos</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">L.</span></div>
                            <input type="text" id="totalGeneral" class="form-control font-weight-bold text-primary" placeholder="0.00" readonly>
                        </div>
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="deposito"><i class="fas fa-coins text-muted mr-1"></i> Saldo / Depósito</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">L.</span></div>
                            <input type="text" id="deposito" class="form-control font-weight-bold text-danger" placeholder="0.00" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Agregar Detalle de Gasto -->
    <div class="col-12">
        <div class="card card-outline card-info shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> Agregar Detalle de Gasto</h3>
            </div>
            <div class="card-body">
                <form id="formGasto">
                    <div class="row align-items-end">
                        <div class="col-md-2 form-group">
                            <label for="fecha">Fecha Gasto</label>
                            <input type="date" id="fecha" class="form-control" required>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="tipo">Tipo de Gasto</label>
                            <select id="tipo" class="form-control custom-select" required>
                                <option value="" selected disabled>Seleccionar tipo...</option>
                                <option value="Alimentación">Alimentación</option>
                                <option value="Transporte">Transporte</option>
                                <option value="Hospedaje">Hospedaje</option>
                                <option value="Peaje">Peaje</option>
                                <option value="Varios">Varios</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="ciudad">Ciudad</label>
                            <input type="text" id="ciudad" class="form-control" placeholder="Ej. Tegucigalpa" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="monto">Monto</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">L.</span></div>
                                <input type="number" id="monto" class="form-control" placeholder="0.00" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-2 form-group">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-plus mr-1"></i> Agregar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Listado de Gastos Agrupados -->
    <div class="col-12">
        <div class="card card-outline card-secondary shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-list-alt mr-1"></i> Resumen de Gastos por Categoría</h3>
                <button type="button" class="btn btn-primary" onclick="guardarLiquidacion()">
                    <i class="fas fa-save mr-1"></i> Guardar Liquidación
                </button>
            </div>
            <div class="card-body">
                <div id="tablasPorTipo">
                    <div class="text-center text-muted my-4">
                        <i class="fas fa-receipt fa-2x mb-2"></i>
                        <p>No se han agregado gastos a esta liquidación.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <style>
        .group-title {
            background-color: #f4f6f9;
            border-left: 4px solid #17a2b8;
            padding: 8px 12px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            border-radius: 0 4px 4px 0;
        }
        @media print {
            .main-sidebar, .main-header, .card-header, form, button, .btn {
                display: none !important;
            }
            .content-wrapper {
                margin-left: 0 !important;
            }
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let gastos = [];

        // Registrar evento de actualización en tiempo real del saldo asignado
        document.getElementById('Asignado').addEventListener('input', actualizarDeposito);

        document.getElementById('formGasto').addEventListener('submit', function(e) {
            e.preventDefault();

            const fecha = document.getElementById('fecha').value;
            const tipo = document.getElementById('tipo').value;
            const ciudad = document.getElementById('ciudad').value.trim();
            const monto = parseFloat(document.getElementById('monto').value);

            if (fecha && tipo && ciudad && !isNaN(monto) && monto > 0) {
                gastos.push({
                    id: Date.now(),
                    fecha,
                    tipo,
                    ciudad,
                    monto
                });
                mostrarGastosPorTipo();
                this.reset();
                document.getElementById('fecha').focus();
            } else {
                Swal.fire('Atención', 'Por favor complete todos los campos correctamente.', 'warning');
            }
        });

        function eliminarGasto(id) {
            gastos = gastos.filter(g => g.id !== id);
            mostrarGastosPorTipo();
        }

        function mostrarGastosPorTipo() {
            const contenedor = document.getElementById('tablasPorTipo');
            
            if (gastos.length === 0) {
                contenedor.innerHTML = `
                    <div class="text-center text-muted my-4">
                        <i class="fas fa-receipt fa-2x mb-2"></i>
                        <p>No se han agregado gastos a esta liquidación.</p>
                    </div>`;
                document.getElementById('totalGeneral').value = "0.00";
                actualizarDeposito();
                return;
            }

            contenedor.innerHTML = '';

            const agrupado = {};
            gastos.forEach(g => {
                if (!agrupado[g.tipo]) agrupado[g.tipo] = [];
                agrupado[g.tipo].push(g);
            });

            let totalGeneral = 0;

            for (const tipo in agrupado) {
                const registros = agrupado[tipo];
                let subtotal = 0;

                let html = `
                    <div class="group-title text-info">
                        <i class="fas fa-tag mr-1"></i> ${tipo}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered mb-3">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 20%;">Fecha</th>
                                    <th>Ciudad</th>
                                    <th style="width: 25%;">Monto</th>
                                    <th style="width: 10%;" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>`;

                registros.forEach(r => {
                    html += `
                        <tr>
                            <td>${r.fecha}</td>
                            <td>${r.ciudad}</td>
                            <td>L. ${r.monto.toFixed(2)}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-xs" onclick="eliminarGasto(${r.id})" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>`;
                    subtotal += r.monto;
                });

                totalGeneral += subtotal;

                html += `
                            <tr class="table-active">
                                <td colspan="2" class="text-right"><strong>Subtotal ${tipo}:</strong></td>
                                <td colspan="2"><strong>L. ${subtotal.toFixed(2)}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>`;

                contenedor.innerHTML += html;
            }

            document.getElementById('totalGeneral').value = totalGeneral.toFixed(2);
            actualizarDeposito();
        }

        function actualizarDeposito() {
            const asignado = parseFloat(document.getElementById("Asignado").value) || 0;
            const total = parseFloat(document.getElementById("totalGeneral").value) || 0;

            const deposito = asignado - total;
            document.getElementById("deposito").value = deposito.toFixed(2);
        }

        function imprimir() {
            window.print();
        }

        function guardarLiquidacion() {
            const ciudad = document.getElementById('visita').value;
            const fecha_entrega = document.getElementById('fecha_visita').value;
            const asignado = parseFloat(document.getElementById('Asignado').value) || 0;
            const total = parseFloat(document.getElementById('totalGeneral').value) || 0;
            const deposito = parseFloat(document.getElementById('deposito').value) || 0;

            if (gastos.length === 0) {
                Swal.fire('Atención', 'Debe agregar al menos un gasto a la liquidación.', 'warning');
                return;
            }

            if (!ciudad) {
                Swal.fire('Campo requerido', 'Por favor especifique la Ciudad Destino.', 'warning');
                return;
            }

            const URL = "{{ route('admin.liquidaciones.store') }}";

            fetch(URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ciudad,
                    fecha_entrega,
                    asignado,
                    total,
                    deposito,
                    gastos
                })
            })
            .then(res => {
                if (!res.ok) throw new Error("Error en la respuesta del servidor");
                return res.json();
            })
            .then(data => {
                Swal.fire('¡Éxito!', 'La liquidación ha sido guardada correctamente.', 'success');
                gastos.length = 0;
                document.getElementById('visita').value = '';
                document.getElementById('Asignado').value = '';
                mostrarGastosPorTipo();
            })
            .catch(error => {
                Swal.fire('Error', 'Ocurrió un problema al guardar la liquidación.', 'error');
            });
        }
    </script>
@endsection