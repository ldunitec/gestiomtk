@extends('adminlte::page')

@section('title', 'Historial de Apunte')

@section('content_header')
<h1><i class="fas fa-history"></i> Historial de: {{ $apunte->titulo }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">

        <a href="{{ route('admin.apuntes.index') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Volver
        </a>

        <table id="tablaHistorial" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Contraseña Anterior</th>
                    <th>Contraseña Nueva</th>
                    <th>Cambiado Por</th>
                    <th>Motivo</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historial as $item)
                <tr>
                    <td>
                        @role('ADMINISTRADOR')
                            {{ Crypt::decryptString($item->password_anterior) }}
                        @else
                            ******
                        @endrole
                    </td>
                    <td>
                        @role('SuperAdmin|Administrador TI')
                            {{ Crypt::decryptString($item->password_nueva) }}
                        @else
                            ******
                        @endrole
                    </td>
                    <td>{{ $item->cambiadoPor->name ?? '' }}</td>
                    <td>{{ $item->motivo }}</td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
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
    $('#tablaHistorial').DataTable({
        responsive: true,
        autoWidth: false,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        }
    });
});
</script>
@stop
