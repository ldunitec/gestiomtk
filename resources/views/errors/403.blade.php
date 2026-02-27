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
<img src="https://www.megatk.net/web/image/297044-e78e8e78/logo%20mega.png" alt="Logo Mega" style="margin: 80px;">
        <h3>Esta tratando de acceder a una pagina no autorizada</h3>
        <p>por favor, contacta al administrador para recibir ayuda</p>
       <a href="{{ url()->previous() }}" class="btn btn-primary">Regresar</a>

    </div>
@endsection

