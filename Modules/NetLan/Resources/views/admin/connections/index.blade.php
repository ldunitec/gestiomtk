@extends('adminlte::page')

@section('content_header')
    <h1>Listado de conexiones</h1>
@stop

@section('content')
    @php
        // Resúmenes de los registros cargados en esta vista.
        $connectionRows = collect($conexiones);
        $connectionCount = $connectionRows->count();
        $switchCount = $connectionRows->pluck('sw')->filter()->unique()->count();
        // Los identificadores incluyen el puerto: SW-A-49 => SW-A.
        $switchDeviceCount = $connectionRows->pluck('sw')->filter()->map(function ($value) {
            return preg_replace('/-\d+$/', '', $value);
        })->unique()->count();
        $patchPortCount = $connectionRows->pluck('nombre')->filter()->unique()->count();
    @endphp

    <div class="net-connections">
        <header class="connections-intro">
            <div>
                <div class="connections-eyebrow">MEGATK · Infraestructura de red</div>
                <h2>Conexiones registradas</h2>
                <p>Consulta equipos, responsables y conexiones a switches y patch panels.</p>
            </div>
            <div class="connections-toolbar">
                <a href="{{ url('netlan/admin/connections/show') }}" class="btn connection-map-btn">
                    <i class="fas fa-network-wired" aria-hidden="true"></i> Mapa de puertos
                </a>
                <button type="button" class="btn btn-primary connection-create-btn" data-toggle="modal" data-target="#crearPortsModal">
                    <i class="fas fa-plus" aria-hidden="true"></i> Nueva conexión
                </button>
            </div>
        </header>

        <div class="connections-metrics">
            <div class="connection-metric">
                <span class="metric-icon"><i class="fas fa-project-diagram" aria-hidden="true"></i></span>
                <div><strong>{{ $connectionCount }}</strong><span>Conexiones registradas</span></div>
            </div>
            <div class="connection-metric">
                <span class="metric-icon"><i class="fas fa-network-wired" aria-hidden="true"></i></span>
                <div><strong>{{ $switchDeviceCount }}</strong><span>Switches / routers referenciados</span></div>
            </div>
            <div class="connection-metric">
                <span class="metric-icon"><i class="fas fa-ethernet" aria-hidden="true"></i></span>
                <div><strong>{{ $switchCount }}</strong><span>Puertos SW asignados</span></div>
            </div>
            <div class="connection-metric">
                <span class="metric-icon"><i class="fas fa-th" aria-hidden="true"></i></span>
                <div><strong>{{ $patchPortCount }}</strong><span>Puertos de patch panel asignados</span></div>
            </div>
        </div>

        <section class="connections-card" aria-labelledby="connections-table-title">
            <header class="connections-card-header">
                <div><h3 id="connections-table-title">Inventario de conexiones</h3><p>Estado y ubicación de cada equipo en la red.</p></div>
                <span class="connections-count">{{ $connectionCount }} registros</span>
            </header>
            <div class="connections-table-scroll" role="region" aria-label="Tabla de conexiones; desplazamiento horizontal" tabindex="0">
                {{-- Se conserva example1 para la configuración DataTables del parcial existente. --}}
                <table id="example1" class="table table-bordered table-hover table-striped table-sm connections-table">
                    <caption class="sr-only">Conexiones registradas con equipo, responsable, direcciones de red, puertos y acciones.</caption>
                    <thead>
                        <tr>
                            <th scope="col">N.º</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Asignado a</th>
                            <th scope="col">Departamento</th>
                            <th scope="col">Equipo</th>
                            <th scope="col">Dirección IP</th>
                            <th scope="col">Dirección MAC</th>
                            <th scope="col">Patch panel</th>
                            <th scope="col">Puerto SW</th>
                            <th scope="col">Comentario</th>
                            <th scope="col">Actualizado</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($conexiones as $conexion)
                            @php
                                $equipment = $conexion->equipo;
                                $employee = optional($equipment)->empleado;
                                $employeeName = trim((optional($employee)->nombres ?? '') . ' ' . (optional($employee)->apellidos ?? ''));
                                $employeeDept = trim((optional($employee)->departamento->nombre  ?? ''));
                                $equipmentType = optional($equipment)->tipo;
                                $equipmentState = optional($equipment)->estado;
                                $active = $equipmentState === 'Activo';
                            @endphp
                            <tr>
                                <td class="connection-number">{{ $loop->iteration }}</td>
                                <td><span class="connection-type">{{ $equipmentType ?: 'Sin tipo' }}</span></td>
                                <td><span class="connection-person">{{ $employeeName ?: 'Sin asignar' }}</span></td>
                                <td><span class="connection-person">{{ $employeeDept ?: 'Sin asignar' }}</span></td>
                                <td><span class="connection-equipment">{{ optional($equipment)->nombre ?: 'Sin equipo' }}</span></td>
                                <td><span class="connection-address">{{ optional($equipment)->ip_address ?: '—' }}</span></td>
                                <td><span class="connection-address connection-mac">{{ optional($equipment)->mac_address ?: '—' }}</span></td>
                                <td><span class="connection-port patch-port">{{ $conexion->nombre ?: '—' }}</span></td>
                                <td><span class="connection-port switch-port">{{ $conexion->sw ?: '—' }}</span></td>
                                <td><span class="connection-comment" title="{{ $conexion->comentario }}">{{ $conexion->comentario ?: '—' }}</span></td>
                                <td class="connection-date" data-order="{{ optional($conexion->updated_at)->timestamp ?? 0 }}">
                                    @if ($conexion->updated_at)
                                        <span>{{ $conexion->updated_at->format('d/m/Y') }}</span>
                                        <small>{{ $conexion->updated_at->format('H:i') }}</small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if ($equipment)
                                        {{-- Se mantienen clase, data-id y clases Bootstrap del cambio de estado existente. --}}
                                        <a href="javascript:void(0)" class="btn btn-sm toggle-estado {{ $active ? ($equipmentType === 'PC' ? 'btn-primary' : 'btn-success') : 'btn-secondary' }}"
                                            data-id="{{ $conexion->id }}" role="button" aria-label="Cambiar estado del equipo {{ $equipment->nombre }}">
                                            <i class="fas fa-desktop" aria-hidden="true"></i>
                                            {{ $active ? 'Activo' : ($equipmentState ?: 'Sin estado') }}
                                        </a>
                                    @else
                                        <span class="connection-missing">Sin equipo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="connection-actions">
                                        <a href="{{ url('netlan/admin/connections/' . $conexion->id . '/edit') }}" class="btn btn-sm connection-edit" title="Editar conexión" aria-label="Editar conexión {{ $conexion->id }}">
                                            <i class="fas fa-pencil-alt" aria-hidden="true"></i>
                                        </a>
                                        <form action="{{ url('netlan/admin/connections', $conexion->id) }}" method="post" class="connection-delete-form" id="miFormulario{{ $conexion->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm connection-delete" title="Eliminar conexión" aria-label="Eliminar conexión {{ $conexion->id }}">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <footer class="connections-card-footer">
                <span><i class="fas fa-info-circle" aria-hidden="true"></i> El estado corresponde al registro del equipo en el sistema.</span>
                <span class="connections-mobile-hint">Desliza la tabla para ver todas las columnas →</span>
            </footer>
        </section>
    </div>

    @include('netlan::admin.connections.parciales.modal')
@stop

@section('css')
    @include('netlan::admin.connections.parciales.css')
    <style>
.net-connections { --nc-ink:#152536; --nc-muted:#657687; --nc-line:#dbe3eb; color:var(--nc-ink); padding:8px 0 28px; }
.net-connections * { box-sizing:border-box; }
.net-connections .connections-intro { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:18px; margin-bottom:22px; }
.net-connections .connections-eyebrow { color:#087f8c; font-size:11px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase; margin-bottom:7px; }
.net-connections .connections-intro h2 { font-size:25px; font-weight:700; margin:0 0 6px; letter-spacing:-.5px; }
.net-connections .connections-intro p { color:var(--nc-muted); font-size:13px; margin:0; }
.net-connections .connections-toolbar { display:flex; flex-wrap:wrap; gap:9px; }
.net-connections .connections-toolbar .btn { padding:10px 15px; border-radius:7px; font-size:13px; font-weight:600; display:inline-flex; gap:8px; align-items:center; }
.net-connections .connection-map-btn { color:#325166; background:#fff; border:1px solid #cbd8e3; }
.net-connections .connection-map-btn:hover { background:#eff5f8; color:#0b7183; }
.net-connections .connection-create-btn { background:#087f8c; border-color:#087f8c; color:#fff; }
.net-connections .connection-create-btn:hover { background:#066875; border-color:#066875; }
.net-connections .connections-metrics { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:22px; }
.net-connections .connection-metric { display:flex; align-items:center; gap:13px; background:#fff; border:1px solid var(--nc-line); border-radius:10px; padding:19px 17px; }
.net-connections .metric-icon { display:flex; align-items:center; justify-content:center; background:#eaf5f5; color:#087f8c; width:42px; height:42px; border-radius:10px; flex:0 0 42px; font-size:17px; }
.net-connections .connection-metric strong { display:block; font-size:27px; line-height:1.1; margin-bottom:5px; font-weight:700; }
.net-connections .connection-metric div>span { display:block; font-size:11px; color:var(--nc-muted); line-height:1.4; }
.net-connections .connections-card { background:#fff; border:1px solid var(--nc-line); border-radius:11px; overflow:hidden; box-shadow:0 4px 14px #15253608; }
.net-connections .connections-card-header { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:20px; border-bottom:1px solid #e6edf2; }
.net-connections .connections-card-header h3 { font-size:16px; font-weight:700; margin:0 0 4px; }
.net-connections .connections-card-header p { font-size:12px; color:var(--nc-muted); margin:0; }
.net-connections .connections-count { white-space:nowrap; font-size:11px; color:#4c6476; background:#f0f5f8; padding:5px 10px; border:1px solid #e2eaf0; border-radius:20px; }
.net-connections .connections-table-scroll { overflow-x:auto; padding:16px 20px; scrollbar-width:thin; }
.net-connections .connections-table { width:100%!important; min-width:1430px; border:0; margin:0; color:#33485a; font-size:12px; }
.net-connections .connections-table thead th { background:#f3f6f9; color:#5a6f80; font-size:10px; letter-spacing:.6px; text-transform:uppercase; padding:13px 10px; border:0; border-bottom:1px solid #dbe4eb; white-space:nowrap; }
.net-connections .connections-table tbody td { padding:13px 10px; vertical-align:middle; border:0; border-bottom:1px solid #eaf0f4; }
.net-connections .connections-table.table-striped tbody tr:nth-of-type(odd) { background:#fff; }
.net-connections .connections-table.table-striped tbody tr:nth-of-type(even) { background:#fbfcfd; }
.net-connections .connections-table.table-hover tbody tr:hover { background:#f0f8fa; }
.net-connections .connection-number { text-align:center; font-size:11px; color:#8393a0; }
.net-connections .connection-type { display:inline-block; background:#eef2f6; border:1px solid #e0e7ef; color:#506376; border-radius:5px; padding:3px 7px; font-size:10px; font-weight:700; white-space:nowrap; }
.net-connections .connection-person { display:block; min-width:130px; max-width:190px; font-weight:500; }
.net-connections .connection-equipment { display:block; min-width:125px; max-width:185px; color:#152536; font-weight:700; }
.net-connections .connection-address { font-family:ui-monospace,SFMono-Regular,Consolas,monospace; font-size:11px; white-space:nowrap; color:#3d5365; }
.net-connections .connection-mac { color:#758694; font-size:10px; }
.net-connections .connection-port { display:inline-block; border:1px solid #dce5ed; border-radius:5px; padding:4px 7px; font-family:ui-monospace,SFMono-Regular,Consolas,monospace; font-size:10px; font-weight:600; white-space:nowrap; }
.net-connections .patch-port { background:#f7f9fb; color:#556777; }
.net-connections .switch-port { background:#edf7f8; color:#087f8c; border-color:#cae4e6; }
.net-connections .connection-comment { display:block; width:150px; color:#7b8a97; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; }
.net-connections .connection-date { white-space:nowrap; color:#4e6475; font-size:11px; }
.net-connections .connection-date small { display:block; font-size:10px; color:#8a99a5; margin-top:2px; }
.net-connections .toggle-estado { white-space:nowrap; border-radius:20px; padding:4px 9px; font-size:10px; font-weight:600; }
.net-connections .toggle-estado.btn-primary,.net-connections .toggle-estado.btn-success { background:#e9f8ef; color:#18734d; border-color:#c6e9d4; }
.net-connections .toggle-estado.btn-secondary { background:#f1f3f6; color:#647484; border-color:#dbe2e9; }
.net-connections .toggle-estado i { margin-right:3px; font-size:9px; }
.net-connections .connection-missing { display:inline-block; color:#84939f; font-size:11px; white-space:nowrap; }
.net-connections .connection-actions { display:flex; justify-content:center; gap:6px; }
.net-connections .connection-actions form { margin:0; }
.net-connections .connection-actions .btn { width:29px; height:29px; display:flex; align-items:center; justify-content:center; border-radius:5px; font-size:10px; }
.net-connections .connection-edit { color:#397493; background:#eff5f9; border:1px solid #dce8f0; }
.net-connections .connection-edit:hover { background:#dcecf5; color:#1c5978; }
.net-connections .connection-delete { color:#c65767; background:#fff4f5; border:1px solid #f3dce1; }
.net-connections .connection-delete:hover { background:#fbe0e5; color:#a92b43; }
.net-connections .connections-card-footer { display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; background:#f8fafc; border-top:1px solid #e7edf3; padding:12px 20px; font-size:11px; color:var(--nc-muted); }
.net-connections .connections-card-footer i { color:#8195a4; margin-right:5px; }
.net-connections .dataTables_wrapper { font-size:12px; color:var(--nc-muted); }
.net-connections .dataTables_wrapper .row { margin-bottom:8px; }
.net-connections .dataTables_wrapper input,.net-connections .dataTables_wrapper select { border:1px solid #cddbe6; border-radius:5px; }
.net-connections .dataTables_wrapper .dt-buttons .btn { border-radius:5px; font-size:11px; margin:0 4px 5px 0; }
.net-connections .page-item.active .page-link { background:#087f8c; border-color:#087f8c; }
.net-connections .page-link { color:#446777; }
.net-connections a:focus-visible,.net-connections button:focus-visible { outline:2px solid #087f8c; outline-offset:3px; }
@media(min-width:1700px) { .net-connections .connections-mobile-hint { display:none; } }
@media(max-width:1100px) { .net-connections .connections-metrics { grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media(max-width:575px) { .net-connections .connections-intro h2 { font-size:22px; } .net-connections .connections-toolbar { width:100%; } .net-connections .connections-toolbar .btn { flex:1; justify-content:center; padding:10px 9px; font-size:12px; } .net-connections .connections-metrics { gap:9px; } .net-connections .connection-metric { gap:9px; padding:14px 10px; align-items:flex-start; } .net-connections .metric-icon { width:30px; height:30px; flex-basis:30px; font-size:13px; border-radius:7px; } .net-connections .connection-metric strong { font-size:23px; } .net-connections .connection-metric div>span { font-size:10px; } .net-connections .connections-card-header { padding:15px; } .net-connections .connections-card-header p { max-width:210px; } .net-connections .connections-table-scroll { padding:12px; } .net-connections .connections-card-footer { padding:12px 15px; } }
@media print { .net-connections .connections-toolbar,.net-connections .connection-actions,.net-connections .connections-mobile-hint { display:none; } .net-connections .connections-table-scroll { overflow:visible; padding:0; } .net-connections .connections-table { min-width:0; font-size:9px; } .net-connections .connections-table th,.net-connections .connections-table td { padding:5px; } .net-connections .connection-equipment,.net-connections .connection-person { min-width:0; } .net-connections .connection-comment { width:auto; white-space:normal; } .net-connections .connections-card { box-shadow:none; } }
    </style>
@stop

@section('js')
    @include('netlan::admin.connections.parciales.js')
    <script>
        // Un único manejador, también compatible con las filas redibujadas por DataTables.
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form.matches('.net-connections .connection-delete-form')) return;
            event.preventDefault();
            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                Swal.fire({
                    title: '¿Eliminar esta conexión?',
                    text: 'Se eliminará el registro de la conexión seleccionada.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar conexión',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#bc4055',
                    cancelButtonColor: '#657687',
                    focusCancel: true
                }).then(function (result) {
                    if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
                });
            } else if (window.confirm('¿Eliminar esta conexión?')) {
                HTMLFormElement.prototype.submit.call(form);
            }
        });
    </script>
@stop
