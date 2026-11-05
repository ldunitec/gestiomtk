@extends('mikrotik::layout')
@section('mt_content')
<div class="card"><div class="card-header"><h2 class="h5 mb-0">Lote #{{ $lote->id }} · {{ $lote->router->nombre }}</h2></div><div class="card-body">
<p>{{ $lote->visitante }} · {{ $lote->cantidad }} fichas · @if($lote->inicio_vigencia === 'permanente')Empleado · Sin vencimiento
@elseif($lote->inicio_vigencia === 'primer_ingreso'){{ $lote->dias }} días desde el primer ingreso de cada ficha
@else{{ $lote->dias }} días desde su creación (fichas anteriores)@endif</p>
@php($imprimibles = $lote->fichas->filter(fn ($f) => $f->imprimible())->count())
<div class="border rounded bg-light p-3 mb-3">
<h3 class="h6"><i class="fas fa-print" aria-hidden="true"></i> Impresión del lote</h3>
<p>{{ $imprimibles }} de {{ $lote->cantidad }} fichas disponibles. Puede imprimir todas, elegir varias o usar el botón de cada ficha.</p>
@if($imprimibles)
<div class="d-flex flex-wrap align-items-center">
<a class="btn btn-primary mr-2 mb-2" target="_blank" rel="noopener" href="{{ route('mt.imprimir', $lote) }}">Imprimir lote completo ({{ $imprimibles }})</a>
<form id="impresion-seleccion" method="GET" action="{{ route('mt.imprimir', $lote) }}" target="_blank" rel="noopener" class="mb-2">
<input type="hidden" name="seleccion" value="1"><button class="btn btn-outline-primary">Imprimir seleccionadas</button>
</form></div>
<p class="small mb-0">Se abrirá una vista para imprimir o guardar como PDF. La impresión no activa ni reinicia la vigencia.</p>
@else
<p class="mb-0">No hay fichas disponibles para imprimir. Revise el estado y la publicación de este lote.</p>
@endif
</div>
<div class="d-flex flex-wrap"><form method="POST" action="{{ route('mt.publicar', $lote) }}" class="mr-2 mb-2">@csrf <button class="btn btn-outline-info">Verificar / reintentar publicación</button></form><form method="POST" action="{{ route('mt.conciliar', $lote->router) }}" class="mb-2">@csrf <button class="btn btn-outline-secondary">Sincronizar primeros ingresos y bloqueos</button></form></div>
<p class="text-muted">Las fichas con error conservan sus credenciales y fecha original al reintentar. La revocación solo queda confirmada cuando el router responde.</p>
<div class="table-responsive"><table class="table"><thead><tr><th>@if($imprimibles)<input type="checkbox" id="seleccionar-todas" aria-label="Seleccionar todas las fichas disponibles">@endif</th><th>Usuario / clave</th><th>Vence</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
@foreach($lote->fichas as $f)<tr><td>@if($f->imprimible())<input type="checkbox" form="impresion-seleccion" name="fichas[]" value="{{ $f->id }}" class="ficha-seleccion" aria-label="Seleccionar ficha {{ $f->usuario }}">@endif</td><td><strong>{{ $f->usuario }}</strong>@if($f->imprimible())<br><code>{{ $f->clave }}</code>@endif</td><td>{{ $f->vigenciaVisible() }}@if($f->activada_at)<br><small>Primer ingreso: {{ $f->activada_at->copy()->timezone(config('mikrotik_visitantes.timezone'))->format('d/m/Y H:i:s') }}</small>@endif</td><td>{{ $f->estadoVisible() }} @if($f->error)<p class="small text-danger">{{ $f->error }}</p>@endif</td><td>
@if($f->imprimible())<a class="btn btn-sm btn-outline-primary mb-2" target="_blank" rel="noopener" href="{{ route('mt.imprimir', ['lote'=>$lote,'ficha_id'=>$f->id]) }}">Imprimir ficha</a>@endif
@if(!$f->revocada_at)<details><summary class="text-danger">Revocar ficha</summary><p class="small">Se bloqueará este usuario y se desconectarán sus sesiones.</p><form method="POST" action="{{ route('mt.revocar', $f) }}">@csrf <button class="btn btn-outline-danger btn-sm">Confirmar revocación</button></form></details>@endif
</td></tr>@endforeach
</tbody></table></div></div></div>
@stop



@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const todas = document.getElementById('seleccionar-todas');
    const fichas = Array.from(document.querySelectorAll('.ficha-seleccion'));
    const formulario = document.getElementById('impresion-seleccion');
    if (!todas || !formulario) return;
    todas.addEventListener('change', function () {
        fichas.forEach(f => { f.checked = todas.checked; });
        todas.indeterminate = false;
    });
    fichas.forEach(f => f.addEventListener('change', function () {
        const n = fichas.filter(f => f.checked).length;
        todas.checked = n === fichas.length;
        todas.indeterminate = n > 0 && n < fichas.length;
    }));
    formulario.addEventListener('submit', function (event) {
        if (!fichas.some(f => f.checked)) {
            event.preventDefault();
            alert('Seleccione al menos una ficha para imprimir.');
        }
    });
});
</script>
@stop
