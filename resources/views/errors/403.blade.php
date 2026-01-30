@extends('adminlte::page')

@section('title', '403- Acceso no autorizado')


@section('content_header')
    <center>
        <h1 class="text-danger"><b>Acceso no autorizado</b></h1>
    </center>
    <hr>
@endsection

@section('content')
    <div class="text-center">
        <i></i>
        <img src="{{url('/img/logo_mega.png')}}" alt="" style="margin: 100px">
        <h3>Esta tratando de acceder a una pagina no autorizada</h3>
        <p>por favor, contacta al administrador para recibir ayuda</p>
       <a href="{{ url()->previous() }}" class="btn btn-primary">Regresar</a>

    </div>
@endsection

