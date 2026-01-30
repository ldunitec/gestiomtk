@extends('adminlte::page')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Levantamiento de Inventario</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalInventario">
            <i class="fas fa-plus"></i> Nuevo Registro
        </button>
    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped table-responsive" id="tablaInventario">
        <thead>
            <tr>
                <th>Localidad</th>
                <th>IP</th>
                <th>RC-308</th>
                <th>Fases</th>
                <th>Fibra</th>
                <th>Ping</th>
                <th>Camaras</th>
                <th>Fuente</th>
                <th>Switch</th>
                <th>X-COM</th>
                <th>SG-116</th>
                <th>Observ</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventarios as $inv)
                <tr>
                    <td>{{ $inv->localidad }}</td>
                    <td>{{ $inv->ip }}</td>
                    <td>{{ $inv->rc }}</td>
                    <td>{{ $inv->fases }}</td>
                    <td>{{ $inv->fibra }}</td>
                    <td>{{ $inv->ping }}</td>
                    <td>{{ $inv->camaras }}</td>
                    <td>{{ $inv->fuente }}</td>
                    <td>{{ $inv->switchPoE }}</td>
                    <td>{{ $inv->xcom }}</td>
                    <td>{{ $inv->sg116 }}</td>
                    <td>{{ $inv->observaciones }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btnEditar" data-id="{{ $inv->id }}"
                            data-json='@json($inv)'>
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @include('admin.inventarioBTR.modal')


@stop

@section('js')
<script>
$(function () {

    $('#tablaInventario').DataTable();

    $('#localidad').on('change', function () {
        $('#ip').val($(this).find(':selected').data('ip') || '');
    });

    $('.btnEditar').on('click', function () {
        let data = $(this).data('json');

        $('#modalInventario').modal('show');
        $('#formInventario').attr('action', '/admin/inventarioBTR/' + data.id);
        $('#method').val('PUT');

        $('#id').val(data.id);
        $('#localidad').val(data.localidad).change();
        $('#ip').val(data.ip);
        $('#rc').val(data.rc);
        $('#fases').val(data.fases);
        $('#fibra').val(data.fibra);
                $('input[name="fibra"][value="'+data.fibra+'"]').prop('checked', true);
        
        $('#ping').val(data.ping);
          $('input[name="ping"][value="'+data.ping+'"]').prop('checked', true);

        $('#camaras').val(data.camaras);
        $('#fuente').val(data.fuente);
            $('input[name="fuente"][value="'+data.fuente+'"]').prop('checked', true);
        $('#switchPoE').val(data.switchPoE);
          $('input[name="switchPoE"][value="'+data.switchPoE+'"]').prop('checked', true);
        $('#xcom').val(data.xcom);
         $('input[name="xcom"][value="'+data.xcom+'"]').prop('checked', true);
        $('#sg116').val(data.sg116);
         $('input[name="sg116"][value="'+data.sg116+'"]').prop('checked', true);
        $('#observaciones').val(data.observaciones);
    });

});
</script>
@stop

