@extends('mikrotik::layout')
@section('mt_content')
<div class="card"><div class="card-header">Crear fichas de visitantes</div><div class="card-body"><form class="row" method="POST" action="{{ route('mt.crear') }}">@csrf <input type="hidden" name="solicitud_uuid" value="{{ $solicitud }}">
<div class="form-group col-md-4"><label for="nuevo-router">Router</label><select required id="nuevo-router" name="router_id" class="form-control"><option value="">Seleccione</option>@foreach($routers as $r)<option value="{{ $r->id }}" @disabled(!$r->preparado_at)>{{ $r->nombre }} {{ $r->preparado_at ? '' : '(falta preparar)' }}</option>@endforeach</select></div>
<div class="form-group col-md-2"><label for="dias">Vigencia</label><select id="dias" name="dias" class="form-control"><option value="1">1 día · 24 h</option><option value="2">2 días · 48 h</option><option value="7">1 semana · 168 h</option></select></div>
<div class="form-group col-md-2"><label for="cantidad">Cantidad</label><input id="cantidad" required name="cantidad" type="number" min="1" max="20" value="1" class="form-control"></div>
<div class="form-group col-md-4"><label for="visitante">Visitante / referencia (opcional)</label><input id="visitante" name="visitante" maxlength="100" class="form-control"></div>
<div class="col-12"><p>La vigencia comienza ahora, aunque el visitante se conecte después. Solo se imprimen fichas confirmadas en el router.</p><button class="btn btn-primary">Crear y publicar fichas</button></div>
</form></div></div>
<form class="row mb-3" method="GET">
<div class="col-md-3 mb-2"><label class="sr-only" for="filtro-router">Router</label><select id="filtro-router" name="router_id" class="form-control"><option value="">Todos los routers</option>@foreach($routers as $r)<option value="{{ $r->id }}" @selected(request('router_id') == $r->id)>{{ $r->nombre }}</option>@endforeach</select></div>
<div class="col-md-3 mb-2"><label class="sr-only" for="estado">Estado</label><select id="estado" name="estado" class="form-control"><option value="">Todos los estados</option>@foreach(['activa'=>'Activa','pendiente'=>'Pendiente','error'=>'Error de publicación','vencida'=>'Vencida','revocacion_pendiente'=>'Revocación pendiente','revocada'=>'Revocada'] as $v=>$l)<option value="{{ $v }}" @selected(request('estado') === $v)>{{ $l }}</option>@endforeach</select></div>
<div class="col-md-3 mb-2"><label class="sr-only" for="q">Usuario</label><input id="q" name="q" class="form-control" placeholder="Buscar usuario" maxlength="100" value="{{ request('q') }}"></div><div class="col-md-3"><button class="btn btn-outline-primary">Filtrar</button></div></form>
<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Usuario</th><th>Router / visitante</th><th>Vencimiento</th><th>Estado</th><th>Lote</th></tr></thead><tbody>
@forelse($fichas as $f)<tr><td>{{ $f->usuario }}</td><td>{{ $f->router->nombre }}<br>{{ $f->lote->visitante }}</td><td>{{ $f->vence_at->timezone(config('mikrotik_visitantes.timezone'))->format('d/m/Y H:i:s') }}</td><td>{{ $f->estadoVisible() }}</td><td><a href="{{ route('mt.lote', $f->lote_id) }}" class="btn btn-sm btn-outline-primary">Abrir lote #{{ $f->lote_id }}</a></td></tr>@empty<tr><td colspan="5">No hay fichas.</td></tr>@endforelse
</tbody></table></div>{{ $fichas->links('pagination::bootstrap-4') }}
@stop
