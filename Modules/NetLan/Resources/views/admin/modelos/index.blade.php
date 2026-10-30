@extends('adminlte::page')
@section('title', 'Listado de Modelos')
@section('content_header')
    <h1>Listado de modelos</h1>
@stop
@section('content')
    @php $modelCount = count($modelos); @endphp
    <div class="net-models">
        <header class="models-intro">
            <div><div class="models-eyebrow">MEGATK · Catálogo de equipos</div><h2>Modelos registrados</h2><p>Consulta y administra los modelos asociados a cada marca.</p></div>
            <button type="button" class="btn model-create" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus" aria-hidden="true"></i> Nuevo modelo</button>
        </header>
        <div class="models-summary"><span class="models-summary-icon"><i class="fas fa-cubes" aria-hidden="true"></i></span><div><strong>{{ $modelCount }}</strong><span>{{ $modelCount === 1 ? 'Modelo registrado' : 'Modelos registrados' }}</span></div><p>Catálogo disponible para registrar equipos.</p></div>
        <section class="models-card" aria-labelledby="models-table-title">
            <header class="models-card-header"><div><h3 id="models-table-title">Catálogo de modelos</h3><p>Marca, nombre y acciones para cada registro.</p></div><span class="models-count">{{ $modelCount }} registros</span></header>
            @if ($modelCount === 0)<div class="models-empty"><i class="fas fa-cubes" aria-hidden="true"></i><strong>Aún no hay modelos registrados.</strong><span>Usa “Nuevo modelo” para agregar el primer registro.</span></div>@endif
            <div class="models-table-scroll" tabindex="0" role="region" aria-label="Tabla de modelos; desplazamiento horizontal">
                {{-- Se conserva example1 para la configuración DataTables del parcial existente. --}}
                <table id="example1" class="table table-bordered table-hover table-striped table-sm w-100 models-table">
                    <caption class="sr-only">Listado de modelos con su marca y acciones de edición y eliminación.</caption>
                    <thead><tr><th scope="col" class="model-number">N.º</th><th scope="col">Marca</th><th scope="col">Nombre del modelo</th><th scope="col" class="model-action-heading">Acciones</th></tr></thead>
                    <tbody>
                        @foreach ($modelos as $modelo)
                            <tr>
                                <td class="model-number">{{ $loop->iteration }}</td>
                                <td><span class="model-brand-label {{ $modelo->marca ? '' : 'is-missing' }}">{{ optional($modelo->marca)->nombre ?: 'Sin marca' }}</span></td>
                                <td><div class="model-identity"><span class="model-icon"><i class="fas fa-cube" aria-hidden="true"></i></span><strong>{{ $modelo->nombre }}</strong></div></td>
                                <td>
                                    <div class="model-actions" role="group" aria-label="Acciones del modelo {{ $modelo->nombre }}">
                                        <button type="button" class="btn btn-outline-success btn-edit" data-toggle="modal" data-target="#editModal" data-id="{{ $modelo->id }}" data-nombre="{{ $modelo->nombre }}" data-marca_id="{{ $modelo->marca_id }}" title="Editar modelo" aria-label="Editar modelo {{ $modelo->nombre }}"><i class="fas fa-pencil-alt" aria-hidden="true"></i><span>Editar</span></button>
                                        <form action="{{ route('netlan.admin.modelos.destroy', $modelo->id) }}" method="POST" class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar modelo" aria-label="Eliminar modelo {{ $modelo->nombre }}"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <footer class="models-card-footer"><span><i class="fas fa-info-circle" aria-hidden="true"></i> Cada modelo se vincula a una marca para identificar los equipos.</span><span class="models-mobile-hint">Desliza para ver las acciones →</span></footer>
        </section>
    </div>
    @include('netlan::admin.modelos.parciales.modal')
@stop
@section('css')
    @include('netlan::admin.modelos.parciales.css')
    <style>

.net-models { --model-ink:#152536; --model-muted:#657687; --model-line:#dbe3eb; color:var(--model-ink); padding:8px 0 28px; }
.net-models * { box-sizing:border-box; }
.net-models .models-intro { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:18px; margin-bottom:22px; }
.net-models .models-eyebrow { color:#087f8c; font-size:11px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase; margin-bottom:7px; }
.net-models h2 { font-size:25px; font-weight:700; letter-spacing:-.5px; margin:0 0 6px; }
.net-models .models-intro p { font-size:13px; color:var(--model-muted); margin:0; }
.net-models .model-create { display:inline-flex; gap:8px; align-items:center; padding:10px 16px; border-radius:7px; color:#fff; background:#087f8c; border:1px solid #087f8c; font-size:13px; font-weight:600; }
.net-models .model-create:hover { background:#066875; }
.net-models .models-summary { display:flex; align-items:center; gap:15px; padding:20px 23px; background:#fff; border:1px solid var(--model-line); border-radius:11px; margin-bottom:22px; }
.net-models .models-summary-icon { display:flex; justify-content:center; align-items:center; flex:0 0 44px; height:44px; border-radius:10px; background:#eaf5f5; color:#087f8c; font-size:18px; }
.net-models .models-summary strong { display:block; font-size:28px; line-height:1.1; font-weight:700; margin-bottom:5px; }
.net-models .models-summary div>span { display:block; font-size:12px; color:var(--model-muted); }
.net-models .models-summary p { margin:0 0 0 auto; color:#8193a1; font-size:12px; }
.net-models .models-card { background:#fff; border:1px solid var(--model-line); border-radius:11px; overflow:hidden; box-shadow:0 4px 14px #15253608; }
.net-models .models-card-header { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:20px 23px; border-bottom:1px solid #e6edf2; }
.net-models h3 { font-size:16px; font-weight:700; margin:0 0 5px; }
.net-models .models-card-header p { color:var(--model-muted); font-size:12px; margin:0; }
.net-models .models-count { white-space:nowrap; font-size:11px; color:#536b7b; background:#f0f5f8; border:1px solid #e2eaf0; border-radius:20px; padding:5px 10px; }
.net-models .models-table-scroll { overflow-x:auto; padding:16px 23px; scrollbar-width:thin; }
.net-models .models-table { width:100%!important; min-width:600px; border:0; margin:0; color:#3a5062; font-size:13px; }
.net-models .models-table thead th { background:#f3f6f9; color:#5a6f80; font-size:10px; text-transform:uppercase; letter-spacing:.6px; padding:13px 12px; border:0; border-bottom:1px solid #dbe4eb; white-space:nowrap; }
.net-models .models-table tbody td { padding:13px 12px; vertical-align:middle; border:0; border-bottom:1px solid #eaf0f4; }
.net-models .models-table.table-striped tbody tr:nth-of-type(odd) { background:#fff; }
.net-models .models-table.table-striped tbody tr:nth-of-type(even) { background:#fbfcfd; }
.net-models .models-table.table-hover tbody tr:hover { background:#f0f8fa; }
.net-models .model-number { width:65px; text-align:center; color:#8393a0; font-size:11px; }
.net-models .model-action-heading { width:150px; text-align:center; }
.net-models .model-identity { display:flex; gap:11px; align-items:center; }
.net-models .model-identity strong { color:#294357; font-size:13px; font-weight:600; overflow-wrap:anywhere; }
.net-models .model-icon { display:flex; align-items:center; justify-content:center; width:31px; height:31px; flex:0 0 31px; border:1px solid #e0e8ef; border-radius:8px; background:#f3f7fa; color:#8397a7; font-size:11px; }
.net-models .model-actions { display:flex; align-items:center; justify-content:center; gap:7px; }
.net-models .model-actions form { margin:0; }
.net-models .model-actions .btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; height:31px; padding:6px 10px; border-radius:5px; font-size:11px; }
.net-models .model-actions .btn-outline-success { color:#397493; background:#eff5f9; border-color:#dce8f0; }
.net-models .model-actions .btn-outline-success:hover { background:#dcecf5; }
.net-models .model-actions .btn-outline-danger { color:#c65767; background:#fff4f5; border-color:#f3dce1; }
.net-models .model-actions .btn-outline-danger:hover { background:#fbe0e5; }
.net-models .models-card-footer { display:flex; flex-wrap:wrap; gap:9px; justify-content:space-between; background:#f8fafc; border-top:1px solid #e7edf3; color:var(--model-muted); padding:12px 23px; font-size:11px; }
.net-models .models-card-footer i { margin-right:5px; color:#8195a4; }
.net-models .models-mobile-hint { display:none; }
.net-models .models-empty { display:flex; flex-direction:column; align-items:center; gap:8px; padding:28px 18px; background:#f8fbfd; border-bottom:1px solid #e6edf2; color:#8195a4; }
.net-models .models-empty>i { font-size:22px; margin-bottom:5px; }
.net-models .models-empty strong { font-size:13px; color:#4e687a; }
.net-models .models-empty span { font-size:12px; }
.net-models .dataTables_wrapper { font-size:12px; color:var(--model-muted); }
.net-models .dataTables_wrapper .row { margin-bottom:8px; }
.net-models .dataTables_wrapper input,.net-models .dataTables_wrapper select { border:1px solid #cddbe6; border-radius:5px; }
.net-models .dataTables_wrapper .dt-buttons .btn { border-radius:5px; font-size:11px; margin:0 4px 5px 0; }
.net-models .page-item.active .page-link { background:#087f8c; border-color:#087f8c; }
.net-models .page-link { color:#446777; }
.net-models button:focus-visible { outline:2px solid #087f8c; outline-offset:3px; }
@media(max-width:575px) { .net-models h2 { font-size:22px; } .net-models .models-summary { padding:17px; flex-wrap:wrap; gap:12px; } .net-models .models-summary p { width:100%; margin:3px 0 0; font-size:11px; } .net-models .models-card-header { padding:16px; } .net-models .models-table-scroll { padding:12px; } .net-models .models-card-footer { padding:12px 16px; } .net-models .models-mobile-hint { display:inline; } }
@media print { .net-models .model-create,.net-models .model-actions,.net-models .models-mobile-hint { display:none; } .net-models .models-table-scroll { overflow:visible; padding:0; } .net-models .models-table { min-width:0; } .net-models .models-card { box-shadow:none; } }
    
.net-models .model-brand-label { display:inline-block; font-size:11px; font-weight:600; border:1px solid #d6e9ec; border-radius:6px; background:#eef7f8; color:#087f8c; padding:5px 9px; }
.net-models .model-brand-label.is-missing { background:#f1f3f6; border-color:#dbe2e9; color:#84939f; }
    </style>
@stop
@section('js')
    @include('netlan::admin.modelos.parciales.js')
    <script>

        $(document).ready(function () {
            // La delegación conserva el manejo de filas redibujadas por DataTables.
            $(document).on('submit', '.net-models .form-eliminar', function (event) {
                event.preventDefault();
                var form = this;
                if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                    Swal.fire({
                        title: '¿Eliminar este modelo?',
                        text: 'Se eliminará el registro de el modelo seleccionado.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#bc4055',
                        cancelButtonColor: '#657687',
                        confirmButtonText: 'Eliminar modelo',
                        cancelButtonText: 'Cancelar',
                        focusCancel: true
                    }).then(function (result) {
                        if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
                    });
                } else if (window.confirm('¿Eliminar este modelo?')) {
                    HTMLFormElement.prototype.submit.call(form);
                }
            });
        });
        </script>
@stop
Get-NetAdapter -IncludeHidden | Where-Object {$_.Name -match "Bridge|Wi-Fi|WIFI"} | Format-Table Name,Status,InterfaceDescription,MacAddress
Get-NetAdapter -IncludeHidden | 
Where-Object {$_.InterfaceDescription -match "Bridge|Virtual Switch|Wireless"} |
Format-Table Name,Status,InterfaceDescription,MacAddress