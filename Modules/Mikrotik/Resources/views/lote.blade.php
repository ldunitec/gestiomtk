@extends('mikrotik::layout')
@section('mt_content')
<div class="card"><div class="card-header"><h2 class="h5 mb-0">Lote #{{ $lote->id }} · {{ $lote->router->nombre }}</h2></div><div class="card-body">
<p>{{ $lote->visitante }} · {{ $lote->cantidad }} fichas · @if($lote->inicio_vigencia === 'permanente')Empleado · Sin vencimiento@elseif($lote->inicio_vigencia === 'primer_ingreso'){{ $lote->dias }} días desde el primer ingreso de cada ficha@else{{ $lote->dias }} días desde su creación (fichas anteriores)@endif</p>
<div class="d-flex flex-wrap"><a class="btn btn-primary mr-2 mb-2" target="_blank" rel="noopener" href="{{ route('mt.imprimir', $lote) }}">Imprimir fichas vigentes</a><form method="POST" action="{{ route('mt.publicar', $lote) }}" class="mr-2 mb-2">@csrf <button class="btn btn-outline-info">Verificar / reintentar publicación</button></form><form method="POST" action="{{ route('mt.conciliar', $lote->router) }}" class="mb-2">@csrf <button class="btn btn-outline-secondary">Sincronizar primeros ingresos y bloqueos</button></form></div>
<p class="text-muted">Las fichas con error conservan sus credenciales y fecha original al reintentar. La revocación solo queda confirmada cuando el router responde.</p>
<div class="table-responsive"><table class="table"><thead><tr><th>Usuario / clave</th><th>Vence</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
@foreach($lote->fichas as $f)<tr><td><strong>{{ $f->usuario }}</strong>@if($f->imprimible())<br><code>{{ $f->clave }}</code>@endif</td><td>{{ $f->vigenciaVisible() }}@if($f->activada_at)<br><small>Primer ingreso: {{ $f->activada_at->copy()->timezone(config('mikrotik_visitantes.timezone'))->format('d/m/Y H:i:s') }}</small>@endif</td><td>{{ $f->estadoVisible() }} @if($f->error)<p class="small text-danger">{{ $f->error }}</p>@endif</td><td>
@if($f->imprimible())<a class="btn btn-sm btn-outline-primary mb-2" target="_blank" rel="noopener" href="{{ route('mt.imprimir', ['lote'=>$lote,'ficha_id'=>$f->id]) }}">Imprimir ficha</a>@endif
@if(!$f->revocada_at)<details><summary class="text-danger">Revocar ficha</summary><p class="small">Se bloqueará este usuario y se desconectarán sus sesiones.</p><form method="POST" action="{{ route('mt.revocar', $f) }}">@csrf <button class="btn btn-outline-danger btn-sm">Confirmar revocación</button></form></details>@endif
</td></tr>@endforeach
</tbody></table></div></div></div>
@stop

