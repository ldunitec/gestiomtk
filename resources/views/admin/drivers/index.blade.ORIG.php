@extends('adminlte::page')

@section('title', 'Listado Drivers')

@section('content_header')
    <h1>Drivers y Manuales</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">

        <table class="table table-bordered table-striped" id="drivers">
            <thead>
                <tr>
                    <th>Marca</th>
                    <th>Categoría</th>
                    <th>Modelo</th>
                    <th>Windows</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                <tr>
                    <td>{{ $file->DeviceModel->brand->name }}</td>
                    <td>{{ $file->DeviceModel->category->name }}</td>
                    <td>{{ $file->DeviceModel->name }}</td>
                    <td>
                      
                        <span class="badge badge-info">{{ strtoupper($file->type) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('drivers.download',$file) }}"
                           class="btn btn-sm btn-success">
                           <i class="fas fa-download"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@stop

@section('js')
<script>
$(function () {
    $('#drivers').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        }
    });
});
</script>
@stop
