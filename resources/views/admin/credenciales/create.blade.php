@extends('adminlte::page')

@section('content')

<form action="{{ route('admin.credenciales.store') }}" method="POST" enctype="multipart/form-data">

@csrf

<label>Nombre</label>
<input type="text" name="nombre" class="form-control">

<label>DNI</label>
<input type="text" name="dni" class="form-control">

<label>Foto</label>
<input type="file" name="foto" class="form-control">

<br>

<button class="btn btn-primary">Guardar</button>

</form>

@endsection