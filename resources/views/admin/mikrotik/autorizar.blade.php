@extends('adminlte::page')

@section('content_header')
<h1>Autorizar Cliente</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.mikrotik.autorizar') }}" method="POST">
    @csrf
    <input type="text" name="ip" class="form-control mb-2" placeholder="IP" required>
    <input type="text" name="mac" class="form-control mb-2" placeholder="MAC" required>
    <input type="text" name="comment" class="form-control mb-2" placeholder="Comentario">
    <button class="btn btn-success">Autorizar</button>
</form>
@stop
