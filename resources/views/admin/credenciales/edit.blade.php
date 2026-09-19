@extends('adminlte::page')

@section('content')

<form action="{{ route('admin.credenciales.update',$credencial->id) }}" method="POST" enctype="multipart/form-data">

@csrf
@method('PUT')

<label>Nombre</label>
<input type="text" name="nombre" class="form-control" value="{{ $credencial->nombre }}">

<label>DNI</label>
<input type="text" name="dni" class="form-control" value="{{ $credencial->dni }}">

<label>Foto</label>
<input type="file" name="foto">

@if($credencial->foto)
<img src="/fotos/{{ $credencial->foto }}" width="80">
@endif

<br>

<button class="btn btn-primary">Actualizar</button>

</form>

@endsection