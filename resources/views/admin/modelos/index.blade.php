@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Modelos </b></h1>
    <hr>
@stop


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">modelos registrados</h3>

                <div class="card-tools">
                    <!-- Botón para abrir el modal -->
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">
                        Nuevo modelo
                    </button>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="example1" class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                        <tr>
                            <th style="text-align: center">Nro</th>
                            <th style="text-align: center">Marca</th>
                            <th style="text-align: center">Nombre</th>
                            <th style="text-align: center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $contador = 1;
                        @endphp
                        @foreach ($modelos as $modelo)
                            <tr>
                                <td style="text-align: center">{{ $contador++ }}</td>
                                <td>{{ $modelo->marca->nombre }}</td>
                                <td>{{ $modelo->nombre }}</td>
                                <td style="text-align: center">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button class="btn btn-success btn-sm" data-toggle="modal"
                                            data-target="#editModal" data-id="{{ $modelo->id }}"
                                            data-nombre="{{ $modelo->nombre }}" data-marca_id="{{ $modelo->marca_id }}">
                                            <i class="fas fa-pencil-alt"></i> Editar
                                        </button>
                                        <form action="{{ url('/admin/modelos', $modelo->id) }}" method="post"
                                            onclick="preguntar{{ $modelo->id }}(event)"
                                            id="miFormulario{{ $modelo->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
@include('admin.modelos.parciales.modal');


@stop

@section('css')
@include('admin.modelos.parciales.css');

@stop

@section('js')
@include('admin.modelos.parciales.js');
@stop
