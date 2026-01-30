@extends('adminlte::page')

@section('content_header')

    {{-- <h1><b>Listado de equipos </b></h1> --}}
    <hr>
@stop

@section('content')


    <div class="col-md-12">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title">DRIVER</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Marca</th>
                            <th>Categoría</th>
                            <th>Modelo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($files as $file)
                            <tr>
                                <td>{{ $file->name }}</td>
                                <td>{{ $file->brand }}</td>
                                <td>{{ $file->category }}</td>
                                <td>{{ $file->model }}</td>
                                <td>{{ $file->tipo }}</td>
                                <td>
                                    <a href="{{ route('files.download', $file) }}"
                                        class="btn btn-sm btn-success">Descargar</a>
                                    <form method="POST" action="{{ route('files.destroy', $file) }}" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
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
    <div class="col-md-12'">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title">FIRMWARE</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Marca</th>
                            <th>Categoría</th>
                            <th>Modelo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($files as $file)
                            <tr>
                                <td>{{ $file->name }}</td>
                                <td>{{ $file->brand }}</td>
                                <td>{{ $file->category }}</td>
                                <td>{{ $file->model }}</td>
                                <td>{{ $file->tipo }}</td>
                                <td>
                                    <a href="{{ route('files.download', $file) }}"
                                        class="btn btn-sm btn-success">Descargar</a>
                                    <form method="POST" action="{{ route('files.destroy', $file) }}" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
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




@endsection
