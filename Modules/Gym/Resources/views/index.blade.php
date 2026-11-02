@extends('gym::layout')
@section('gym_content')
<div class="row">
@foreach(['Socios activos' => $socios, 'Membresías vigentes y pagadas' => $vigentes, 'Vencen en los próximos 7 días' => $vencen, 'Asistencias de hoy' => $visitas] as $titulo => $cantidad)
<div class="col-md-3 col-sm-6"><div class="small-box bg-info"><div class="inner"><h3>{{ $cantidad }}</h3><p>{{ $titulo }}</p></div></div></div>
@endforeach
</div>
<div class="card"><div class="card-header">Últimos movimientos</div><div class="card-body">@include('gym::tabla-accesos')</div></div>
@stop
