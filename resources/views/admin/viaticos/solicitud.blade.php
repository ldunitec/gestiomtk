
@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
  <div class="card">
    <img class="card-img-top" src="holder.js/100x180/" alt="Title" />
    <div class="card-body">
       <table class="table" id="tablaSolicitudes">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Ciudad</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
</table>

    </div>
  </div>
  
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop