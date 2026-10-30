@extends('adminlte::page')
@section('title', 'Listado de Marcas')
@section('content_header')
    <h1>Listado de marcas</h1>
@stop
@section('content')
    @php $brandCount = count($marcas); @endphp
    <div class="net-brands">
        <header class="brands-intro">
            <div><div class="brands-eyebrow">MEGATK · Catálogo de equipos</div><h2>Marcas registradas</h2><p>Administra las marcas utilizadas en el inventario de equipos.</p></div>
            <button type="button" class="btn brand-create" data-toggle="modal" data-target="#createModal"><i class="fas fa-plus" aria-hidden="true"></i> Nueva marca</button>
        </header>
        <div class="brands-summary"><span class="brands-summary-icon"><i class="fas fa-tags" aria-hidden="true"></i></span><div><strong>{{ $brandCount }}</strong><span>{{ $brandCount === 1 ? 'Marca registrada' : 'Marcas registradas' }}</span></div><p>Catálogo disponible para registrar modelos y equipos.</p></div>
        <section class="brands-card" aria-labelledby="brands-table-title">
            <header class="brands-card-header"><div><h3 id="brands-table-title">Catálogo de marcas</h3><p>Consulta, edita o elimina los registros del catálogo.</p></div><span class="brands-count">{{ $brandCount }} registros</span></header>
            @if ($brandCount === 0)<div class="brands-empty"><i class="fas fa-tags" aria-hidden="true"></i><strong>Aún no hay marcas registradas.</strong><span>Usa “Nueva marca” para agregar el primer registro.</span></div>@endif
            <div class="brands-table-scroll" tabindex="0" role="region" aria-label="Tabla de marcas; desplazamiento horizontal">
                {{-- Se conserva example1 para la configuración DataTables del parcial existente. --}}
                <table id="example1" class="table table-bordered table-hover table-striped table-sm w-100 brands-table">
                    <caption class="sr-only">Listado de marcas con acciones de edición y eliminación.</caption>
                    <thead><tr><th scope="col" class="brand-number">N.º</th><th scope="col">Nombre de la marca</th><th scope="col" class="brand-action-heading">Acciones</th></tr></thead>
                    <tbody>
                        @foreach ($marcas as $marca)
                            <tr>
                                <td class="brand-number">{{ $loop->iteration }}</td>
                                <td><div class="brand-identity"><span class="brand-icon"><i class="fas fa-tag" aria-hidden="true"></i></span><strong>{{ $marca->nombre }}</strong></div></td>
                                <td>
                                    <div class="brand-actions" role="group" aria-label="Acciones de la marca {{ $marca->nombre }}">
                                        <button type="button" class="btn btn-outline-success btn-edit" data-toggle="modal" data-target="#editModal" data-id="{{ $marca->id }}" data-nombre="{{ $marca->nombre }}" title="Editar marca" aria-label="Editar marca {{ $marca->nombre }}"><i class="fas fa-pencil-alt" aria-hidden="true"></i><span>Editar</span></button>
                                        <form action="{{ route('netlan.admin.marcas.destroy', $marca->id) }}" method="POST" class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar marca" aria-label="Eliminar marca {{ $marca->nombre }}"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <footer class="brands-card-footer"><span><i class="fas fa-info-circle" aria-hidden="true"></i> Las marcas se utilizan en el catálogo de modelos y equipos.</span><span class="brands-mobile-hint">Desliza para ver las acciones →</span></footer>
        </section>
    </div>
    @include('netlan::admin.marcas.parciales.modal')
@stop
@section('css')
    @include('netlan::admin.marcas.parciales.css')
    <style>
.net-brands { --brand-ink:#152536; --brand-muted:#657687; --brand-line:#dbe3eb; color:var(--brand-ink); padding:8px 0 28px; }
.net-brands * { box-sizing:border-box; }
.net-brands .brands-intro { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:18px; margin-bottom:22px; }
.net-brands .brands-eyebrow { color:#087f8c; font-size:11px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase; margin-bottom:7px; }
.net-brands h2 { font-size:25px; font-weight:700; letter-spacing:-.5px; margin:0 0 6px; }
.net-brands .brands-intro p { font-size:13px; color:var(--brand-muted); margin:0; }
.net-brands .brand-create { display:inline-flex; gap:8px; align-items:center; padding:10px 16px; border-radius:7px; color:#fff; background:#087f8c; border:1px solid #087f8c; font-size:13px; font-weight:600; }
.net-brands .brand-create:hover { background:#066875; }
.net-brands .brands-summary { display:flex; align-items:center; gap:15px; padding:20px 23px; background:#fff; border:1px solid var(--brand-line); border-radius:11px; margin-bottom:22px; }
.net-brands .brands-summary-icon { display:flex; justify-content:center; align-items:center; flex:0 0 44px; height:44px; border-radius:10px; background:#eaf5f5; color:#087f8c; font-size:18px; }
.net-brands .brands-summary strong { display:block; font-size:28px; line-height:1.1; font-weight:700; margin-bottom:5px; }
.net-brands .brands-summary div>span { display:block; font-size:12px; color:var(--brand-muted); }
.net-brands .brands-summary p { margin:0 0 0 auto; color:#8193a1; font-size:12px; }
.net-brands .brands-card { background:#fff; border:1px solid var(--brand-line); border-radius:11px; overflow:hidden; box-shadow:0 4px 14px #15253608; }
.net-brands .brands-card-header { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:20px 23px; border-bottom:1px solid #e6edf2; }
.net-brands h3 { font-size:16px; font-weight:700; margin:0 0 5px; }
.net-brands .brands-card-header p { color:var(--brand-muted); font-size:12px; margin:0; }
.net-brands .brands-count { white-space:nowrap; font-size:11px; color:#536b7b; background:#f0f5f8; border:1px solid #e2eaf0; border-radius:20px; padding:5px 10px; }
.net-brands .brands-table-scroll { overflow-x:auto; padding:16px 23px; scrollbar-width:thin; }
.net-brands .brands-table { width:100%!important; min-width:480px; border:0; margin:0; color:#3a5062; font-size:13px; }
.net-brands .brands-table thead th { background:#f3f6f9; color:#5a6f80; font-size:10px; text-transform:uppercase; letter-spacing:.6px; padding:13px 12px; border:0; border-bottom:1px solid #dbe4eb; white-space:nowrap; }
.net-brands .brands-table tbody td { padding:13px 12px; vertical-align:middle; border:0; border-bottom:1px solid #eaf0f4; }
.net-brands .brands-table.table-striped tbody tr:nth-of-type(odd) { background:#fff; }
.net-brands .brands-table.table-striped tbody tr:nth-of-type(even) { background:#fbfcfd; }
.net-brands .brands-table.table-hover tbody tr:hover { background:#f0f8fa; }
.net-brands .brand-number { width:65px; text-align:center; color:#8393a0; font-size:11px; }
.net-brands .brand-action-heading { width:150px; text-align:center; }
.net-brands .brand-identity { display:flex; gap:11px; align-items:center; }
.net-brands .brand-identity strong { color:#294357; font-size:13px; font-weight:600; overflow-wrap:anywhere; }
.net-brands .brand-icon { display:flex; align-items:center; justify-content:center; width:31px; height:31px; flex:0 0 31px; border:1px solid #e0e8ef; border-radius:8px; background:#f3f7fa; color:#8397a7; font-size:11px; }
.net-brands .brand-actions { display:flex; align-items:center; justify-content:center; gap:7px; }
.net-brands .brand-actions form { margin:0; }
.net-brands .brand-actions .btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; height:31px; padding:6px 10px; border-radius:5px; font-size:11px; }
.net-brands .brand-actions .btn-outline-success { color:#397493; background:#eff5f9; border-color:#dce8f0; }
.net-brands .brand-actions .btn-outline-success:hover { background:#dcecf5; }
.net-brands .brand-actions .btn-outline-danger { color:#c65767; background:#fff4f5; border-color:#f3dce1; }
.net-brands .brand-actions .btn-outline-danger:hover { background:#fbe0e5; }
.net-brands .brands-card-footer { display:flex; flex-wrap:wrap; gap:9px; justify-content:space-between; background:#f8fafc; border-top:1px solid #e7edf3; color:var(--brand-muted); padding:12px 23px; font-size:11px; }
.net-brands .brands-card-footer i { margin-right:5px; color:#8195a4; }
.net-brands .brands-mobile-hint { display:none; }
.net-brands .brands-empty { display:flex; flex-direction:column; align-items:center; gap:8px; padding:28px 18px; background:#f8fbfd; border-bottom:1px solid #e6edf2; color:#8195a4; }
.net-brands .brands-empty>i { font-size:22px; margin-bottom:5px; }
.net-brands .brands-empty strong { font-size:13px; color:#4e687a; }
.net-brands .brands-empty span { font-size:12px; }
.net-brands .dataTables_wrapper { font-size:12px; color:var(--brand-muted); }
.net-brands .dataTables_wrapper .row { margin-bottom:8px; }
.net-brands .dataTables_wrapper input,.net-brands .dataTables_wrapper select { border:1px solid #cddbe6; border-radius:5px; }
.net-brands .dataTables_wrapper .dt-buttons .btn { border-radius:5px; font-size:11px; margin:0 4px 5px 0; }
.net-brands .page-item.active .page-link { background:#087f8c; border-color:#087f8c; }
.net-brands .page-link { color:#446777; }
.net-brands button:focus-visible { outline:2px solid #087f8c; outline-offset:3px; }
@media(max-width:575px) { .net-brands h2 { font-size:22px; } .net-brands .brands-summary { padding:17px; flex-wrap:wrap; gap:12px; } .net-brands .brands-summary p { width:100%; margin:3px 0 0; font-size:11px; } .net-brands .brands-card-header { padding:16px; } .net-brands .brands-table-scroll { padding:12px; } .net-brands .brands-card-footer { padding:12px 16px; } .net-brands .brands-mobile-hint { display:inline; } }
@media print { .net-brands .brand-create,.net-brands .brand-actions,.net-brands .brands-mobile-hint { display:none; } .net-brands .brands-table-scroll { overflow:visible; padding:0; } .net-brands .brands-table { min-width:0; } .net-brands .brands-card { box-shadow:none; } }
    </style>
@stop
@section('js')
    @include('netlan::admin.marcas.parciales.js')
    <script>
        $(document).ready(function () {
            // La delegación conserva el manejo de filas redibujadas por DataTables.
            $(document).on('submit', '.net-brands .form-eliminar', function (event) {
                event.preventDefault();
                var form = this;
                if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                    Swal.fire({
                        title: '¿Eliminar esta marca?',
                        text: 'Se eliminará el registro de la marca seleccionada.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#bc4055',
                        cancelButtonColor: '#657687',
                        confirmButtonText: 'Eliminar marca',
                        cancelButtonText: 'Cancelar',
                        focusCancel: true
                    }).then(function (result) {
                        if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
                    });
                } else if (window.confirm('¿Eliminar esta marca?')) {
                    HTMLFormElement.prototype.submit.call(form);
                }
            });
        });
    </script>
@stop
