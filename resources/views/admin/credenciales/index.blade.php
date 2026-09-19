@extends('adminlte::page')

@section('content')

<a href="{{ route('admin.credenciales.create') }}" class="btn btn-primary">
Nueva Credencial
</a>

<table class="table">
<thead>
<tr>
<th>ID</th>
<th>Nombre</th>
<th>DNI</th>
<th>Foto</th>
<th>Acciones</th>
</tr>
</thead>

<tbody>

@foreach($credenciales as $c)

<tr>
<td>{{ $c->id }}</td>
<td>{{ $c->nombre }}</td>
<td>{{ $c->dni }}</td>

<td>
@if($c->foto)
<img src="/fotos/{{ $c->foto }}" width="50">
@endif
</td>

<td>

<a href="{{ route('admin.credenciales.edit',$c->id) }}"
class="btn btn-warning">Editar</a>

<a href="{{ route('admin.credenciales.imprimir',$c->id) }}"
class="btn btn-success">Imprimir</a>

<form action="{{ route('admin.credenciales.destroy',$c->id) }}"
method="POST"
style="display:inline">

@csrf
@method('DELETE')

<button class="btn btn-danger">Eliminar</button>

</form>

</td>

</tr>

@endforeach

</tbody>
</table>

@endsection