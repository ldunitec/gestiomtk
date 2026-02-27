@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="card">
        <img class="card-img-top" src="holder.js/100x180/" alt="Title" />
        <div class="card-body">
            <form method="POST" action="{{ route('admin.idcards.store') }}" enctype="multipart/form-data">
                @csrf
                <input name="nombre">
                <input name="identificacion">
                <input name="cargo">
                <input type="file" name="foto">
                <button>Guardar</button>
            </form>
        </div>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
