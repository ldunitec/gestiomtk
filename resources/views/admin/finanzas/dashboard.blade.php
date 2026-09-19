@extends('adminlte::page')

@section('content')

<div class="row">

<div class="col-md-3">
<div class="small-box bg-success">
<div class="inner">
<h3>{{ $ingresos }}</h3>
<p>Ingresos</p>
</div>
</div>
</div>

<div class="col-md-3">
<div class="small-box bg-danger">
<div class="inner">
<h3>{{ $egresos }}</h3>
<p>Egresos</p>
</div>
</div>
</div>

<div class="col-md-3">
<div class="small-box bg-info">
<div class="inner">
<h3>{{ $balance }}</h3>
<p>Balance</p>
</div>
</div>
</div>

{{-- <div class="col-md-3">
@foreach($alertas as $a)

<div class="alert alert-warning">

Pago vence {{ $a->fecha_pago }}

</div>

@endforeach
</div> --}}

</div>

@stop