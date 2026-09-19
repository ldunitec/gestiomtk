@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card bg-danger">
                <div class="card-body">
                    @if ($vencidas->count())
                        <div class="alert alert-danger">
                            <strong>Deudas Vencidas:</strong>
                            <ul>
                                @foreach ($vencidas as $d)
                                    <li>{{ $d->name }} - Venció el {{ $d->due_date }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>



        <div class="col-md-6">
            <div class="card bg-danger">
                <div class="card-body">
                    @if ($proximas->count())
                        <div class="alert alert-warning">
                            <strong>Próximas a vencer:</strong>
                            <ul>
                                @foreach ($proximas as $d)
                                    <li>{{ $d->name }} - Vence el {{ $d->due_date }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-md-3">
        <div class="card bg-danger">
            <div class="card-body">
                <h4>Total Deudas</h4>
                <h3>L {{ $totalDeudas }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning">
            <div class="card-body">
                <h4>Tarjetas</h4>
                <h3>L {{ $tarjetas }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info">
            <div class="card-body">
                <h4>Suscripciones</h4>
                <h3>L {{ $suscripciones }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success">
            <div class="card-body">
                <h4>Prestamos</h4>
                <h3>L {{ $prestamos }}</h3>
            </div>
        </div>
    </div>

    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script></script>
@stop
