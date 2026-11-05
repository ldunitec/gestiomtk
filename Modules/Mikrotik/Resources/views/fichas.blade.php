@extends('mikrotik::layout')
@section('mt_content')
<div class="card"><div class="card-header">{{ $empleados ? 'Crear ficha de empleado' : 'Crear fichas de visitantes' }}</div><div class="card-body"><form class="row" method="POST" action="{{ route($empleados ? 'mt.empleados.crear' : 'mt.crear') }}">@csrf <input type="hidden" name="solicitud_uuid" value="{{ $solicitud }}">
<div class="form-group col-md-4"><label for="nuevo-router">Router</label><select required id="nuevo-router" name="router_id" class="form-control"><option value="">Seleccione</option>@foreach($routers as $r)<option value="{{ $r->id }}" @disabled(!$r->preparado_at)>{{ $r->nombre }} {{ $r->preparado_at ? '' : '(falta preparar)' }}</option>@endforeach</select></div>
@unless($empleados)<div class="form-group col-md-2"><label for="dias">Vigencia</label><select id="dias" name="dias" class="form-control"><option value="1">1 día · 24 h</option><option value="2">2 días · 48 h</option><option value="7">1 semana · 168 h</option></select></div>
<div class="form-group col-md-2"><label for="cantidad">Cantidad</label><input id="cantidad" required name="cantidad" type="number" min="1" max="20" value="1" class="form-control"></div>
@endunless<div class="form-group col-md-4"><label for="visitante">{{ $empleados ? 'Nombre del empleado' : 'Visitante / referencia (opcional)' }}</label><input id="visitante" name="visitante" @required($empleados) maxlength="100" class="form-control"></div>
<div class="col-12"><p>{{ $empleados ? 'Acceso sin vencimiento hasta su revocación manual.' : 'Los 1, 2 o 7 días comienzan en el primer ingreso exitoso al Hotspot. Imprimir no activa la vigencia.' }} Solo se imprimen fichas confirmadas en el router.</p><button class="btn btn-primary">Crear y publicar fichas</button></div>
</form></div></div>
@foreach($routers as $r)<form method="POST" action="{{ route('mt.conciliar', $r) }}" class="mb-2">@csrf <button class="btn btn-sm btn-outline-secondary">Sincronizar primeros ingresos y bloqueos · {{ $r->nombre }}</button></form>@endforeach
<p class="small text-muted">El primer ingreso lo registra el router. Sincronice para actualizar las fechas del panel; las reimpresiones conservan el plazo.</p>
<div class="card border-primary"><div class="card-header">Impresión y administración de fichas</div><div class="card-body">
<p>Las opciones masivas abarcan todas las páginas de esta sección{{ request('router_id') ? ', del router filtrado' : '' }}.</p>
<div class="d-flex flex-wrap">
<a class="btn btn-primary mr-2 mb-2" target="_blank" rel="noopener" href="{{ route('mt.fichas.imprimir', ['empleados'=>(int) $empleados, 'router_id'=>request('router_id'), 'modo'=>'todas']) }}">Imprimir todas las activas</a>
<a class="btn btn-outline-primary mr-2 mb-2" target="_blank" rel="noopener" href="{{ route('mt.fichas.imprimir', ['empleados'=>(int) $empleados, 'router_id'=>request('router_id'), 'modo'=>'pendientes']) }}">Imprimir activas sin imprimir</a>
<form id="imprimir-seleccion" method="GET" target="_blank" rel="noopener" action="{{ route('mt.fichas.imprimir') }}" class="mb-2">
<input type="hidden" name="empleados" value="{{ (int) $empleados }}"><input type="hidden" name="router_id" value="{{ request('router_id') }}"><input type="hidden" name="modo" value="seleccion">
<button class="btn btn-outline-primary">Imprimir seleccionadas</button></form>
</div>
<p class="small text-muted">Seleccione las fichas activas sin impresión registrada en esta página. Para reimprimir una ficha ya impresa, abra su lote. Después de imprimir o guardar el PDF, use «Confirmar impresas» en la vista de impresión.</p>
<details><summary class="text-danger">Eliminar todas las fichas con error</summary>
<p>Se eliminarán del listado las fichas con error de esta sección y router filtrado, después de confirmar su bloqueo en el router. Las que no puedan bloquearse se conservarán. El historial se mantiene.</p>
<form method="POST" action="{{ route('mt.fichas.eliminar-errores') }}">@csrf @method('DELETE')
<input type="hidden" name="empleados" value="{{ (int) $empleados }}"><input type="hidden" name="router_id" value="{{ request('router_id') }}">
<label class="d-block"><input required type="checkbox" name="confirmar" value="1"> Confirmo la eliminación de todas las fichas con error de este alcance.</label>
<button class="btn btn-outline-danger">Eliminar fichas con error</button></form></details>
</div></div>
<form class="row mb-3" method="GET">
<div class="col-md-3 mb-2"><label class="sr-only" for="filtro-router">Router</label><select id="filtro-router" name="router_id" class="form-control"><option value="">Todos los routers</option>@foreach($routers as $r)<option value="{{ $r->id }}" @selected(request('router_id') == $r->id)>{{ $r->nombre }}</option>@endforeach</select></div>
<div class="col-md-3 mb-2"><label class="sr-only" for="estado">Estado</label><select id="estado" name="estado" class="form-control"><option value="">Todos los estados</option>@foreach(['activa'=>'Activa','pendiente'=>'Pendiente','error'=>'Error de publicación','vencida'=>'Vencida','revocacion_pendiente'=>'Revocación pendiente','revocada'=>'Revocada'] as $v=>$l)<option value="{{ $v }}" @selected(request('estado') === $v)>{{ $l }}</option>@endforeach</select></div>
<div class="col-md-3 mb-2"><label class="sr-only" for="impresion">Impresión</label><select id="impresion" name="impresion" class="form-control"><option value="">Todas las impresiones</option><option value="pendientes" @selected(request('impresion') === 'pendientes')>Activas sin imprimir</option><option value="impresas" @selected(request('impresion') === 'impresas')>Impresas</option></select></div>
<div class="col-md-3 mb-2"><label class="sr-only" for="q">Usuario o nombre</label><input id="q" name="q" class="form-control" placeholder="Buscar usuario o nombre" maxlength="100" value="{{ request('q') }}"></div><div class="col-md-3"><button class="btn btn-outline-primary">Filtrar</button></div></form>
<div class="table-responsive"><table class="table table-striped"><thead><tr><th><input id="seleccionar-pendientes" type="checkbox" aria-label="Seleccionar activas sin imprimir de esta página"></th><th>Usuario</th><th>Router / nombre</th><th>Vigencia</th><th>Estado</th><th>Impresión</th><th>Acciones</th></tr></thead><tbody>
@forelse($fichas as $f)
<tr>
<td>@if($f->imprimible() && !$f->impresa_at)<input class="ficha-pendiente" form="imprimir-seleccion" name="fichas[]" value="{{ $f->id }}" type="checkbox" aria-label="Seleccionar {{ $f->usuario }}">@endif</td>
<td>{{ $f->usuario }}</td><td>{{ $f->router->nombre }}<br>{{ $f->nombreTitular() }}</td><td>{{ $f->vigenciaVisible() }}</td><td>{{ $f->estadoVisible() }}</td>
<td>@if($f->impresa_at)<span class="badge badge-success">Impresa</span><br><small>{{ $f->impresa_at->copy()->timezone(config('mikrotik_visitantes.timezone'))->format('d/m/Y H:i') }}</small>@else<span class="badge badge-secondary">Sin impresión registrada</span>@endif</td>
<td><a href="{{ route('mt.fichas.editar', $f) }}" class="btn btn-sm btn-outline-secondary mb-1">Editar</a> <a href="{{ route('mt.lote', $f->lote_id) }}" class="btn btn-sm btn-outline-primary mb-1">Abrir lote #{{ $f->lote_id }}</a></td>
</tr>
@empty<tr><td colspan="7">No hay fichas.</td></tr>@endforelse
</tbody></table></div>{{ $fichas->links('pagination::bootstrap-4') }}
<p class="small text-muted">El registro de impresión comienza con esta actualización. Las impresiones anteriores no se pueden detectar automáticamente.</p>
@stop
@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const todas = document.getElementById('seleccionar-pendientes');
    const fichas = Array.from(document.querySelectorAll('.ficha-pendiente'));
    const formulario = document.getElementById('imprimir-seleccion');
    todas.disabled = fichas.length === 0;
    todas.addEventListener('change', function () {
        fichas.forEach(f => { f.checked = todas.checked; });
        todas.indeterminate = false;
    });
    fichas.forEach(f => f.addEventListener('change', function () {
        const n = fichas.filter(f => f.checked).length;
        todas.checked = n > 0 && n === fichas.length;
        todas.indeterminate = n > 0 && n < fichas.length;
    }));
    formulario.addEventListener('submit', function (event) {
        if (!fichas.some(f => f.checked)) { event.preventDefault(); alert('Seleccione al menos una ficha activa sin imprimir.'); }
    });
});
</script>
@stop
