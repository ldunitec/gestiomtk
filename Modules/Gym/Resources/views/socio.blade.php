@extends('gym::layout')
@section('gym_content')
<div class="card"><div class="card-header"><h2 class="h5 mb-0">{{ $socio->nombre }} · <strong>{{ $socio->codigo }}</strong></h2></div><div class="card-body"><form method="POST" action="{{ route('gym.socios.actualizar', $socio) }}">@csrf @method('PUT') @include('gym::form-socio')</form></div></div>
<div class="card"><div class="card-header">Nueva membresía / renovación</div><div class="card-body">
<form class="row" method="POST" action="{{ route('gym.membresias', $socio) }}">@csrf
<div class="form-group col-md-5"><label for="plan_id">Plan</label><select required name="plan_id" id="plan_id" class="form-control"><option value="">Seleccione un plan</option>@foreach($planes as $p)<option value="{{ $p->id }}" @selected(old('plan_id') == $p->id)>{{ $p->nombre }} · {{ $p->dias }} días · {{ config('gym.moneda') }} {{ number_format($p->precio_centavos/100, 2) }} · {{ $p->limite_visitas ?? 'Sin límite de' }} visitas</option>@endforeach</select></div>
<div class="form-group col-md-3"><label for="inicio">Fecha de inicio</label><input required type="date" name="inicio" id="inicio" class="form-control" value="{{ old('inicio', $siguiente) }}" min="{{ \Modules\Gym\Services\MembresiaService::hoy() }}"></div>
<div class="form-group col-md-2"><label for="abono">Pago inicial ({{ config('gym.moneda') }})</label><input required type="number" min="0" max="9999999.99" step="0.01" name="abono" id="abono" value="{{ old('abono','0.00') }}" class="form-control"></div>
<div class="form-group col-md-2 align-self-end"><button class="btn btn-primary" @disabled($planes->isEmpty())>Crear membresía</button></div>
</form><p class="text-muted mb-0">El inicio y el vencimiento son inclusivos. La renovación sugiere el día posterior al vencimiento; no se permiten fechas superpuestas. El acceso se habilita al completar el pago.</p></div></div>
@foreach($membresias as $m)
<div class="card"><div class="card-header"><strong>{{ $m->plan_nombre }}</strong> · {{ $m->inicio }} al {{ $m->fin }} @if($m->anulada)<span class="badge badge-danger">Anulada</span>@elseif($m->pagado_centavos < $m->precio_centavos)<span class="badge badge-warning">Pendiente de pago</span>@else<span class="badge badge-success">Pagada</span>@endif</div><div class="card-body">
<p>Precio: {{ config('gym.moneda') }} {{ number_format($m->precio_centavos/100, 2) }} · Pagado: {{ number_format($m->pagado_centavos/100, 2) }} · Saldo: <strong>{{ number_format(($m->precio_centavos-$m->pagado_centavos)/100, 2) }}</strong> · Límite de visitas: {{ $m->limite_visitas ?? 'Sin límite' }}</p>
@if(!$m->anulada && $m->pagado_centavos < $m->precio_centavos)
<form class="form-inline mb-3" method="POST" action="{{ route('gym.pagos', $m) }}">@csrf <label class="mr-2" for="importe-{{ $m->id }}">Abono</label><input id="importe-{{ $m->id }}" required name="importe" type="number" min="0.01" max="{{ ($m->precio_centavos-$m->pagado_centavos)/100 }}" step="0.01" class="form-control mr-2"><label class="sr-only" for="ref-{{ $m->id }}">Referencia</label><input id="ref-{{ $m->id }}" name="referencia" maxlength="150" class="form-control mr-2" placeholder="Referencia opcional"><button class="btn btn-success">Registrar pago</button></form>
@endif
<details class="mb-3"><summary>Historial de pagos ({{ $m->pagos->count() }})</summary><ul>@foreach($m->pagos as $p)<li>{{ $p->created_at->timezone(config('gym.timezone'))->format('d/m/Y H:i') }} · {{ config('gym.moneda') }} {{ number_format($p->centavos/100,2) }} · {{ $p->referencia }}</li>@endforeach</ul></details>
@if(!$m->anulada)<details><summary class="text-danger">Anular membresía</summary><p>Se bloquearán los próximos ingresos. Los pagos se conservarán y no se registrará un reembolso.</p><form method="POST" action="{{ route('gym.anular', $m) }}">@csrf <button class="btn btn-outline-danger btn-sm">Confirmar anulación de esta membresía</button></form></details>@endif
</div></div>
@endforeach
{{ $membresias->links('pagination::bootstrap-4') }}
@stop
