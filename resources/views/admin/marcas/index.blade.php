@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Marcas </b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">marcas registrados</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">
                            Nueva Marca
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
                                <th style="text-align: center">Nombre</th>
                                <th style="text-align: center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @foreach ($marcas as $marca)
                                <tr>
                                    <td style="text-align: center">{{ $contador++ }}</td>
                                    <td>{{ $marca->nombre }}</td>
                                    <td style="text-align: center">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <button class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#editModal" data-id="{{ $marca->id }}"
                                                data-nombre="{{ $marca->nombre }}">
                                                <i class="fas fa-pencil-alt"></i> Editar
                                            </button>
                                            <form action="{{ url('/admin/marcas', $marca->id) }}" method="post"
                                                onclick="preguntar{{ $marca->id }}(event)"
                                                id="miFormulario{{ $marca->id }}">
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

  

    @include('admin.marcas.parciales.modal');

    </div>
    </div>


@stop

@section('css')
     @include('admin.marcas.parciales.css');
@stop

@section('js')
  @include('admin.marcas.parciales.js');
 
@stop
