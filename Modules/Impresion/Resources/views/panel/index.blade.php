@extends('adminlte::page')
@section('title', 'Panel de impresión')
@section('content_header')
<div class="d-flex flex-wrap justify-content-between align-items-center">
    <div><h1>Panel de impresión</h1><p class="text-muted mb-0">Fotos individuales y lotes por curso y sección.</p></div>
    <a class="btn btn-outline-secondary" href="{{ route('impresion.admin.creys.crey') }}">Administrar e importar alumnos</a>
</div>
@stop
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())
<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<div class="row">
    <div class="col-sm-4"><div class="small-box bg-info"><div class="inner"><h3>{{ $total }}</h3><p>Registros del grupo filtrado</p></div></div></div>
    <div class="col-sm-4"><div class="small-box bg-warning"><div class="inner"><h3>{{ $total - $impresos }}</h3><p>Pendientes de impresión</p></div></div></div>
    <div class="col-sm-4"><div class="small-box bg-success"><div class="inner"><h3>{{ $impresos }}</h3><p>Marcados como impresos</p></div></div></div>
</div>
<div class="card card-outline card-primary">
<form method="GET" action="{{ route('impresion.panel') }}" class="card-body">
    <div class="row">
    @foreach(['curso' => $cursos, 'seccion' => $secciones, 'modalidad' => $modalidades] as $campo => $opciones)
        <div class="col-md-3 form-group">
            <label for="f_{{ $campo }}">{{ ucfirst($campo) }}</label>
            <select class="form-control" id="f_{{ $campo }}" name="{{ $campo }}">
                <option value="">Todos</option>
                @foreach($opciones as $opcion)<option value="{{ $opcion }}" @selected(($filters[$campo] ?? '') === $opcion)>{{ $opcion }}</option>@endforeach
            </select>
        </div>
    @endforeach
        <div class="col-md-3 form-group">
            <label for="f_estado">Estado</label>
            <select class="form-control" id="f_estado" name="estado">
                @foreach(['pendientes' => 'Pendientes', 'impresos' => 'Impresos', 'todos' => 'Todos'] as $valor => $texto)
                    <option value="{{ $valor }}" @selected(($filters['estado'] ?? 'pendientes') === $valor)>{{ $texto }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 form-group"><label for="f_q">Buscar nombre, apellido o DNI</label><input id="f_q" name="q" class="form-control" maxlength="100" value="{{ $filters['q'] ?? '' }}"></div>
        <div class="col-md-6 align-self-end form-group">
            <button class="btn btn-primary">Filtrar / Actualizar panel</button>
            <a class="btn btn-outline-secondary" href="{{ route('impresion.panel') }}">Limpiar filtros</a>
        </div>
    </div>
</form>
</div>
<div id="resultado_zip" class="alert" role="status" hidden></div>
<div class="card">
    <div class="card-header"><h3 class="card-title">Preparar lote · <span id="seleccionados">0</span> seleccionados en esta página</h3></div>
    <form id="form_lote" method="POST" action="{{ route('impresion.preparar') }}" target="_blank" class="card-body">
        @csrf
        @foreach(['curso', 'seccion', 'modalidad', 'q', 'estado'] as $campo)
            <input type="hidden" name="{{ $campo }}" value="{{ $filters[$campo] ?? ($campo === 'estado' ? 'pendientes' : '') }}">
        @endforeach
        <div class="row">
            <div class="col-md-4 form-group"><label for="alcance">Incluir</label><select id="alcance" name="alcance" class="form-control">
                <option value="seleccion">Solo alumnos seleccionados</option>
                <option value="seccion">Toda la sección filtrada (todas las páginas)</option>
            </select><small class="text-muted">Para toda la sección aplica primero curso y sección. Máximo 500 registros por lote.</small></div>
            <div class="col-md-2 form-group"><label for="ancho">Ancho foto (mm)</label><input id="ancho" name="ancho" type="number" class="form-control" value="30" min="20" max="100"></div>
            <div class="col-md-2 form-group"><label for="alto">Alto foto (mm)</label><input id="alto" name="alto" type="number" class="form-control" value="40" min="20" max="100"></div>
            <div class="col-md-4 align-self-center form-group">
                <label><input name="marcar_impresos" type="checkbox" value="1" checked> Marcar como impresos al generar el ZIP</label>
                <small class="d-block text-muted">Solo se marcan fotos incluidas. Desmarca para exportar sin cambiar el estado.</small>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-print"></i> Vista previa e imprimir</button>
        <button type="submit" class="btn btn-success mb-2" data-action="zip" formaction="{{ route('impresion.zip') }}"><i class="fas fa-file-archive"></i> Descargar ZIP</button>
        <small class="d-block text-muted">El ZIP incluye carpetas CURSO / SECCIÓN / MODALIDAD, fotos con NOMBRES APELLIDOS en MAYÚSCULAS y RESUMEN.csv. Límite: 100 MB.</small>
    </form>
</div>
<div class="card"><div class="card-body table-responsive">
<table class="table table-hover table-striped">
    <thead><tr><th><input type="checkbox" id="seleccionar_todos" aria-label="Seleccionar esta página"></th><th>Foto</th><th>Alumno</th><th>Curso / sección</th><th>Estado</th><th>Acciones</th></tr></thead>
    <tbody>
    @forelse($alumnos as $alumno)
        <tr>
            <td><input type="checkbox" name="ids[]" value="{{ $alumno->id }}" form="form_lote" class="alumno_seleccionado" aria-label="Seleccionar {{ $alumno->nombres }} {{ $alumno->apellidos }}"></td>
            <td>@if($disponibles[$alumno->id])<img src="{{ route('impresion.foto', $alumno->id) }}" width="48" height="64" style="object-fit:contain" loading="lazy" alt="Foto de {{ $alumno->nombres }}">@else<span class="badge badge-danger">Sin foto</span>@endif</td>
            <td><strong>{{ mb_strtoupper($alumno->nombres.' '.$alumno->apellidos) }}</strong><br><small>DNI: {{ $alumno->dni }}</small></td>
            <td>{{ $alumno->curso }} · {{ $alumno->seccion }}<br><small>{{ $alumno->modalidad }}</small></td>
            <td>@if($alumno->impreso_at)<span class="badge badge-success">Impreso</span><br><small>{{ $alumno->impreso_at->format('d/m/Y H:i') }}</small>@else<span class="badge badge-warning">Pendiente</span>@endif</td>
            <td>
                @if($disponibles[$alumno->id])
                <form method="POST" action="{{ route('impresion.preparar') }}" target="_blank" class="mb-2">
                    @csrf<input type="hidden" name="alcance" value="seleccion"><input type="hidden" name="estado" value="todos"><input type="hidden" name="ids[]" value="{{ $alumno->id }}">
                    <button class="btn btn-sm btn-primary">Imprimir individual</button>
                </form>
                @endif
                <details><summary class="text-primary">Cargar / reemplazar foto</summary>
                    <form method="POST" action="{{ route('impresion.foto.upload', $alumno->id) }}" enctype="multipart/form-data" class="mt-2">
                        @csrf<input type="file" name="foto" accept="image/jpeg,image/png,image/webp" required class="form-control-file">
                        <small>JPG, PNG o WEBP, máximo 10 MB. Quedará pendiente.</small><br><button class="btn btn-sm btn-outline-primary mt-1">Guardar foto</button>
                    </form>
                </details>
            </td>
        </tr>
    @empty
        <tr><td colspan="6">No hay alumnos con estos filtros. Revisa el estado o importa registros CREY.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $alumnos->links() }}
</div></div>
<div class="card"><div class="card-header"><h3 class="card-title">Últimos lotes</h3></div><div class="card-body table-responsive">
<table class="table table-sm"><thead><tr><th>Fecha</th><th>Acción</th><th>Incluidos</th><th>Sin foto</th><th>Marcados impresos</th></tr></thead>
<tbody>@forelse($lotes as $lote)<tr><td>{{ $lote->created_at }}</td><td>{{ $lote->accion === 'zip' ? 'ZIP generado' : 'Impresión confirmada' }}</td><td>{{ $lote->incluidos }}</td><td>{{ $lote->faltantes }}</td><td>{{ $lote->marcados }}</td></tr>@empty<tr><td colspan="5">Sin lotes registrados.</td></tr>@endforelse</tbody></table>
</div></div>
@stop
@section('js')
<script src="{{ asset('js/impresion-panel.js') }}"></script>
@stop
