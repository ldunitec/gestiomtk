@extends('adminlte::page')

@section('content_header')
 
<h3>Nueva Deuda</h3>
@stop

@section('content')
   
<form action="{{ route('admin.debts.store') }}" method="POST">

@csrf

<div class="mb-3">
<label>Tipo</label>
<select name="type" class="form-control">
<option value="Tarjeta">Tarjeta</option>
<option value="Prestamo">Prestamo</option>
<option value="Suscripcion">Suscripcion</option>
<option value="Reembolso">Reembolso</option>
</select>
</div>

<div class="mb-3">
<label>Nombre</label>
<input type="text" name="name" class="form-control">
</div>

<div class="mb-3">
<label>Monto Total</label>
<input type="number" step="0.01" name="original_amount" class="form-control">
</div>

<div class="mb-3">
<label>Total Cuotas</label>
<input type="number" name="total_installments" class="form-control">
</div>

<div class="mb-3">
<label>Fecha vencimiento</label>
<input type="date" name="due_date" class="form-control">
</div>

<button class="btn btn-success">
Guardar
</button>

</form>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop




