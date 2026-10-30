@extends('adminlte::page')
@section('title', 'Listado de Equipos')
@section('content_header')
    <h1>Listado de equipos</h1>
@stop
@section('content')
    @php
        $activeCount = count($activos);
        $inactiveCount = count($inActivos);
        $equipmentTotal = $activeCount + $inactiveCount;
    @endphp
    <div class="net-equipment">
        <header class="equipment-intro">
            <div><div class="equipment-eyebrow">MEGATK · Infraestructura de red</div><h2>Inventario de equipos</h2><p>Consulta responsables, direcciones de red y estado de los equipos.</p></div>
            <div class="equipment-toolbar">
                <a href="{{ route('netlan.admin.equipos.pdf') }}" target="_blank" rel="noopener" class="btn equipment-pdf"><i class="fas fa-file-pdf" aria-hidden="true"></i> Imprimir PDF</a>
                <button type="button" class="btn equipment-secondary" data-toggle="modal" data-target="#marcaModal"><i class="fas fa-tag" aria-hidden="true"></i> Nueva marca</button>
                <button type="button" class="btn equipment-secondary" data-toggle="modal" data-target="#createModal"><i class="fas fa-cube" aria-hidden="true"></i> Nuevo modelo</button>
                <button type="button" class="btn btn-primary equipment-primary" data-toggle="modal" data-target="#crearEquipoModal"><i class="fas fa-plus" aria-hidden="true"></i> Nuevo equipo</button>
            </div>
        </header>
        <div class="equipment-metrics">
            <div class="equipment-metric"><span class="metric-icon"><i class="fas fa-desktop" aria-hidden="true"></i></span><div><strong>{{ $equipmentTotal }}</strong><span>Equipos registrados</span></div></div>
            <div class="equipment-metric metric-active"><span class="metric-icon"><i class="fas fa-check-circle" aria-hidden="true"></i></span><div><strong>{{ $activeCount }}</strong><span>Equipos activos</span></div></div>
            <div class="equipment-metric metric-inactive"><span class="metric-icon"><i class="fas fa-pause-circle" aria-hidden="true"></i></span><div><strong>{{ $inactiveCount }}</strong><span>Equipos inactivos</span></div></div>
        </div>
        <section class="equipment-card active-card" aria-labelledby="active-title">
            <header class="equipment-card-header"><div><h3 id="active-title"><span class="equipment-dot"></span>Equipos activos</h3><p>Asignación, red y protección de los equipos activos.</p></div><span class="equipment-count">{{ $activeCount }} registros</span></header>
            @if ($activeCount === 0)
                <div class="equipment-empty"><i class="fas fa-info-circle" aria-hidden="true"></i> No hay equipos activos registrados.</div>
            @endif
            <div class="equipment-table-scroll" role="region" tabindex="0" aria-label="Tabla de equipos activos; desplazamiento horizontal">
                <table id="equipoActivo" class="table table-bordered table-hover table-striped table-sm equipment-table">
                            <thead class="">
                                <tr>
                                    <th scope="col" class="text-center" style="width: 50px;">N.º</th>
                                    <th scope="col">Asignado a</th>
                                    <th scope="col">Dirección IP</th>
                                    <th scope="col">Dirección MAC</th>
                                    <th scope="col" class="text-center">Equipo</th>
                                    <th scope="col" class="text-center">Tipo</th>
                                    <th scope="col" class="text-center">Antivirus</th>
                                    <th scope="col" class="text-center">Estado</th>
                                    <th scope="col" class="text-center">Actualizado</th>
                                    <th scope="col" class="text-center" style="width: 100px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activos as $equipo)
                                    <tr>
                                        <td class="text-center align-middle font-weight-bold text-secondary">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="align-middle">
                                            {{ $equipo->empleado ? $equipo->empleado->nombres . ' ' . $equipo->empleado->apellidos : 'Sin asignación' }}
                                        </td>
                                        <td class="align-middle"><code>{{ $equipo->ip_address ?: '—' }}</code></td>
                                        <td class="align-middle"><code>{{ $equipo->mac_address ?: '—' }}</code></td>
                                        <td class="align-middle"><code>{{ $equipo->nombre ?: '—' }}</code></td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-secondary">{{ $equipo->tipo }}</span>
                                        </td>
                                        <td class="text-center align-middle">{{ $equipo->antivirus ?: '—' }}</td>
                                        <td class="text-center align-middle">
                                            <button type="button" 
                                                    class="btn btn-xs toggle-estado {{ $equipo->estado == 'Activo' ? 'btn-success' : 'btn-danger' }}"
                                                    data-id="{{ $equipo->id }}" aria-label="Cambiar estado de {{ $equipo->nombre }}">
                                                {{ $equipo->estado == 'Activo' ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-light border">
                                                {{ $equipo->updated_at ? \Carbon\Carbon::parse($equipo->updated_at)->format('d/m/Y H:i') : '—' }}
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ url('netlan/admin/equipos/' . $equipo->id . '/edit') }}"
                                                   class="btn btn-outline-success" 
                                                   title="Editar equipo" aria-label="Editar equipo {{ $equipo->nombre }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <form action="{{ url('netlan/admin/equipos', $equipo->id) }}"
                                                      method="POST" 
                                                      class="d-inline form-eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar equipo" aria-label="Eliminar equipo {{ $equipo->nombre }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
            </div>
            <footer class="equipment-card-footer"><span>Estado registrado en el inventario del sistema.</span><span>Desliza para ver todas las columnas →</span></footer>
        </section>
        <section class="equipment-card inactive-card" aria-labelledby="inactive-title">
            <header class="equipment-card-header"><div><h3 id="inactive-title"><span class="equipment-dot"></span>Equipos inactivos</h3><p>Equipos registrados como inactivos en el sistema.</p></div><span class="equipment-count">{{ $inactiveCount }} registros</span></header>
            @if ($inactiveCount === 0)
                <div class="equipment-empty"><i class="fas fa-info-circle" aria-hidden="true"></i> No hay equipos inactivos registrados.</div>
            @endif
            <div class="equipment-table-scroll" role="region" tabindex="0" aria-label="Tabla de equipos inactivos; desplazamiento horizontal">
                <table id="equipoInactivo" class="table table-bordered table-hover table-striped table-sm equipment-table">
                            <thead class="">
                                <tr>
                                    <th scope="col" class="text-center" style="width: 40px;">N.º</th>
                                    <th scope="col">Asignado a</th>
                                    <th scope="col">Departamento</th>
                                    <th scope="col">IP / MAC</th>
                                    <th scope="col" class="text-center">Estado</th>
                                    <th scope="col" class="text-center">Modificado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inActivos as $equipo)
                                    <tr>
                                        <td class="text-center align-middle font-weight-bold text-secondary">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="align-middle">
                                            <strong class="equipment-name">{{ $equipo->nombre ?: 'Sin nombre' }}</strong>
<small class="font-weight-bold d-block">
                                                {{ $equipo->empleado ? $equipo->empleado->nombres . ' ' . $equipo->empleado->apellidos : 'Sin asignación' }}
                                            </small>
                                        </td>
                                        <td class="align-middle">
                                            <small class="d-block">IP: <code>{{ $equipo->ip_address ?: '—' }}</code></small>
                                            <small class="d-block text-muted">MAC: {{ $equipo->mac_address ?: '—' }}</small>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button" 
                                                    class="btn btn-xs toggle-estado btn-danger"
                                                    data-id="{{ $equipo->id }}" aria-label="Cambiar estado de {{ $equipo->nombre }}">
                                                Inactivo
                                            </button>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-light border">
                                                {{ $equipo->updated_at ? \Carbon\Carbon::parse($equipo->updated_at)->format('d/m/Y H:i') : '—' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
            </div>
            <footer class="equipment-card-footer"><span>Estado registrado en el inventario del sistema.</span><span>Desliza para ver todas las columnas →</span></footer>
        </section>
    </div>
    @include('netlan::admin.equipos.parciales.modal')
    <div class="modal fade equipment-modal" id="createModal" tabindex="-1" role="dialog" aria-labelledby="crearModeloModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('netlan.admin.modelos.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header text-white equipment-modal-header">
                        <h5 class="modal-title font-weight-bold" id="crearModeloModalLabel">Agregar Nuevo Modelo</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="modelo_marca_id">Marca <span class="text-danger">*</span></label>
                            <select name="marca_id" id="modelo_marca_id" class="form-control" required>
                                <option value="" disabled selected>Seleccione una marca</option>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="equipment-model-name">Nombre del Modelo <span class="text-danger">*</span></label>
                            <input type="text" id="equipment-model-name" class="form-control" name="nombre" placeholder="Ej. ThinkPad L14" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- Modal Rápido: Marca --}}
    <div class="modal fade equipment-modal" id="marcaModal" tabindex="-1" role="dialog" aria-labelledby="crearMarcaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('netlan.admin.marcas.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header text-white equipment-modal-header">
                        <h5 class="modal-title font-weight-bold" id="crearMarcaModalLabel">Agregar Nueva Marca</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nombre_marca">Nombre de la Marca <span class="text-danger">*</span></label>
                            <input type="text" id="nombre_marca" class="form-control" name="nombre" placeholder="Ej. Lenovo, HP, Dell" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
@section('css')
    @include('netlan::admin.equipos.parciales.css')
    <style>

.net-equipment { --eq-ink:#152536; --eq-muted:#657687; --eq-line:#dbe3eb; color:var(--eq-ink); padding:8px 0 28px; }
.net-equipment * { box-sizing:border-box; }
.net-equipment .equipment-intro { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px; margin-bottom:22px; }
.net-equipment .equipment-eyebrow { color:#087f8c; font-size:11px; letter-spacing:1.8px; font-weight:700; text-transform:uppercase; margin-bottom:7px; }
.net-equipment h2 { font-size:25px; font-weight:700; letter-spacing:-.5px; margin:0 0 6px; }
.net-equipment .equipment-intro p { font-size:13px; color:var(--eq-muted); margin:0; }
.net-equipment .equipment-toolbar { display:flex; gap:8px; flex-wrap:wrap; }
.net-equipment .equipment-toolbar .btn { display:inline-flex; gap:7px; align-items:center; padding:10px 13px; border-radius:7px; font-size:12px; font-weight:600; }
.net-equipment .equipment-secondary { background:#fff; border:1px solid #cbd8e3; color:#425f74; }
.net-equipment .equipment-secondary:hover { background:#eef5f8; }
.net-equipment .equipment-pdf { color:#b64d5d; background:#fff; border:1px solid #edd2d8; }
.net-equipment .equipment-pdf:hover { background:#fff0f3; }
.net-equipment .equipment-primary { background:#087f8c; border-color:#087f8c; color:#fff; }
.net-equipment .equipment-primary:hover { background:#066875; }
.net-equipment .equipment-metrics { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:15px; margin-bottom:22px; }
.net-equipment .equipment-metric { background:#fff; border:1px solid var(--eq-line); border-radius:10px; padding:20px; display:flex; gap:14px; align-items:center; }
.net-equipment .metric-icon { flex:0 0 43px; height:43px; display:flex; align-items:center; justify-content:center; border-radius:10px; color:#087f8c; background:#eaf5f5; font-size:18px; }
.net-equipment .metric-active .metric-icon { color:#18734d; background:#e9f8ef; }
.net-equipment .metric-inactive .metric-icon { color:#ad7c2b; background:#fff6e6; }
.net-equipment .equipment-metric strong { display:block; font-size:28px; font-weight:700; line-height:1.1; margin-bottom:5px; }
.net-equipment .equipment-metric div>span { font-size:12px; color:var(--eq-muted); }
.net-equipment .equipment-card { background:#fff; border:1px solid var(--eq-line); border-radius:11px; overflow:hidden; margin-bottom:22px; box-shadow:0 4px 14px #15253608; }
.net-equipment .equipment-card-header { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:20px; border-bottom:1px solid #e6edf2; }
.net-equipment .equipment-card-header h3 { display:flex; align-items:center; gap:8px; font-size:16px; font-weight:700; margin:0 0 5px; }
.net-equipment .equipment-card-header p { color:var(--eq-muted); font-size:12px; margin:0; }
.net-equipment .equipment-dot { background:#21a672; width:8px; height:8px; border-radius:50%; }
.net-equipment .inactive-card .equipment-dot { background:#d49a3e; }
.net-equipment .equipment-count { white-space:nowrap; font-size:11px; color:#536b7b; background:#f0f5f8; padding:5px 10px; border-radius:20px; border:1px solid #e2eaf0; }
.net-equipment .equipment-table-scroll { overflow-x:auto; padding:16px 20px; scrollbar-width:thin; }
.net-equipment .equipment-table { width:100%!important; min-width:1200px; border:0; font-size:12px; color:#3a5062; margin:0; }
.net-equipment .inactive-card .equipment-table { min-width:800px; }
.net-equipment .equipment-table thead th { background:#f3f6f9; color:#5a6f80; font-size:10px; text-transform:uppercase; letter-spacing:.5px; white-space:nowrap; border:0; border-bottom:1px solid #dbe4eb; padding:13px 10px; }
.net-equipment .equipment-table tbody td { vertical-align:middle; padding:14px 10px; border:0; border-bottom:1px solid #eaf0f4; }
.net-equipment .equipment-table.table-striped tbody tr:nth-of-type(odd) { background:#fff; }
.net-equipment .equipment-table.table-striped tbody tr:nth-of-type(even) { background:#fbfcfd; }
.net-equipment .equipment-table.table-hover tbody tr:hover { background:#f0f8fa; }
.net-equipment .equipment-table code { color:#425c70; font-family:ui-monospace,SFMono-Regular,Consolas,monospace; font-size:11px; white-space:nowrap; }
.net-equipment .equipment-name { display:block; margin-bottom:4px; font-size:12px; color:#152536; }
.net-equipment .equipment-table .badge { font-size:10px; border-radius:5px; font-weight:600; padding:5px 8px; }
.net-equipment .equipment-table .badge-secondary { color:#556b7d; background:#eef2f6; border:1px solid #e0e7ef; }
.net-equipment .equipment-table .badge-light { color:#708391; background:#f8fafc; white-space:nowrap; }
.net-equipment .toggle-estado { border-radius:20px; padding:4px 10px; font-size:11px; font-weight:600; white-space:nowrap; }
.net-equipment .toggle-estado.btn-success { background:#e9f8ef; color:#18734d; border-color:#c6e9d4; }
.net-equipment .toggle-estado.btn-danger { background:#fff6e6; color:#976519; border-color:#f1dfb9; }
.net-equipment .equipment-table .btn-group { display:flex; align-items:center; justify-content:center; gap:6px; }
.net-equipment .equipment-table .btn-group form { margin:0; }
.net-equipment .equipment-table .btn-group .btn { display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:5px!important; font-size:11px; }
.net-equipment .equipment-table .btn-outline-success { color:#397493; background:#eff5f9; border-color:#dce8f0; }
.net-equipment .equipment-table .btn-outline-success:hover { background:#dcecf5; }
.net-equipment .equipment-table .btn-outline-danger { color:#c65767; background:#fff4f5; border-color:#f3dce1; }
.net-equipment .equipment-table .btn-outline-danger:hover { background:#fbe0e5; }
.net-equipment .equipment-card-footer { display:flex; flex-wrap:wrap; justify-content:space-between; gap:10px; padding:12px 20px; background:#f8fafc; border-top:1px solid #e7edf3; color:var(--eq-muted); font-size:11px; }
.net-equipment .equipment-empty { padding:20px; font-size:13px; color:#687f91; background:#f8fbfd; border-bottom:1px solid #e6edf2; }
.net-equipment .equipment-empty i { margin-right:7px; }
.net-equipment .dataTables_wrapper { font-size:12px; color:var(--eq-muted); }
.net-equipment .dataTables_wrapper .row { margin-bottom:8px; }
.net-equipment .dataTables_wrapper input,.net-equipment .dataTables_wrapper select { border:1px solid #cddbe6; border-radius:5px; }
.net-equipment .dataTables_wrapper .dt-buttons .btn { font-size:11px; border-radius:5px; margin:0 4px 5px 0; }
.net-equipment .page-item.active .page-link { background:#087f8c; border-color:#087f8c; }
.net-equipment .page-link { color:#446777; }
.net-equipment button:focus-visible,.net-equipment a:focus-visible { outline:2px solid #087f8c; outline-offset:3px; }
.equipment-modal .modal-content { border:0; border-radius:12px; overflow:hidden; }
.equipment-modal .equipment-modal-header { background:#087f8c; padding:18px 22px; }
.equipment-modal .modal-title { font-size:17px; }
.equipment-modal .modal-body { padding:22px; }
.equipment-modal label { font-size:13px; color:#405b6e; }
.equipment-modal .form-control { border:1px solid #cddbe6; border-radius:7px; font-size:14px; }
.equipment-modal .modal-footer { background:#f8fafc; padding:14px 22px; }
.equipment-modal .btn { border-radius:7px; font-size:13px; }
.equipment-modal .btn-primary { background:#087f8c; border-color:#087f8c; }
@media(max-width:767px) { .net-equipment .equipment-metrics { gap:10px; } .net-equipment .equipment-metric { padding:15px; gap:10px; } .net-equipment .equipment-metric div>span { font-size:11px; } .net-equipment .metric-icon { width:32px; flex-basis:32px; height:32px; font-size:14px; } }
@media(max-width:575px) { .net-equipment h2 { font-size:22px; } .net-equipment .equipment-toolbar { width:100%; display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); } .net-equipment .equipment-toolbar .btn { justify-content:center; padding:10px; } .net-equipment .equipment-metrics { grid-template-columns:1fr; } .net-equipment .equipment-metric { padding:13px 16px; } .net-equipment .equipment-metric strong { font-size:24px; } .net-equipment .equipment-card-header { padding:16px; } .net-equipment .equipment-table-scroll { padding:12px; } .net-equipment .equipment-card-footer { padding:12px 16px; } }
@media print { .net-equipment .equipment-toolbar,.net-equipment .equipment-table .btn-group,.net-equipment .equipment-card-footer { display:none; } .net-equipment .equipment-table-scroll { overflow:visible; padding:0; } .net-equipment .equipment-table { min-width:0!important; font-size:9px; } .net-equipment .equipment-table td,.net-equipment .equipment-table th { padding:5px; } .net-equipment .equipment-card { box-shadow:none; } }
    </style>
@stop
@section('js')
    @include('netlan::admin.equipos.parciales.js')
    <script>

// Confirmación delegada, compatible con filas redibujadas por DataTables.
$(document).ready(function () {
    $(document).on('submit', '.net-equipment .form-eliminar', function (event) {
        event.preventDefault();
        var form = this;
        if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
            Swal.fire({
                title: '¿Eliminar este equipo?',
                text: 'Se eliminará el registro del equipo seleccionado.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#bc4055',
                cancelButtonColor: '#657687',
                confirmButtonText: 'Eliminar equipo',
                cancelButtonText: 'Cancelar',
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
            });
        } else if (window.confirm('¿Eliminar este equipo?')) {
            HTMLFormElement.prototype.submit.call(form);
        }
    });
});
    </script>
@stop
