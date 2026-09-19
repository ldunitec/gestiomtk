@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de departamentos</b></h1>
    <hr>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <!-- Card Header -->
            <div class="card-header">
                <h3 class="card-title">Departamentos registrados</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                        data-target="#crearEditarDepartamentoModal">
                        Nuevo departamento
                    </button>
                </div>
            </div>

            <!-- Card Body -->
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
                        @php $contador = 1; @endphp
                        @foreach ($departamentos as $departamento)
                        <tr>
                            <td style="text-align: center">{{ $contador++ }}</td>
                            <td>{{ $departamento->nombre }}</td>
                            <td style="text-align: center">
                                <div class="btn-group" role="group">
                                    <!-- Botón Editar -->
                                    <button class="btn btn-success btn-sm" data-toggle="modal"
                                        data-target="#crearEditarDepartamentoModal"
                                        data-id="{{ $departamento->id }}"
                                        data-nombre="{{ $departamento->nombre }}">
                                        <i class="fas fa-pencil-alt"></i> Editar
                                    </button>

                                    <!-- Formulario Eliminar -->
                                    <form action="{{ url('/admin/departamentos', $departamento->id) }}"
                                        method="POST" class="form-eliminar d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Único: Crear / Editar Departamento -->
<div class="modal fade" id="crearEditarDepartamentoModal" tabindex="-1" role="dialog" aria-labelledby="crearEditarDepartamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form-departamento" method="POST" action="{{ url('/admin/departamentos') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Nuevo Departamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Ingrese nombre" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
{{-- CSS adicional si necesitas --}}
@stop

@section('js')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTables
    $('#example1').DataTable({
        responsive: true,
        autoWidth: false,
    });

    // Abrir modal para CREAR
    $('[data-target="#crearEditarDepartamentoModal"]').on('click', function(){
        var modal = $('#crearEditarDepartamentoModal');
        modal.find('#modal-title').text('Nuevo Departamento');
        modal.find('form').attr('action', '/admin/departamentos');
        modal.find('input[name="nombre"]').val('');
        modal.find('input[name="_method"]').remove(); // Elimina PUT si existía
    });

    // Abrir modal para EDITAR
    $('#example1').on('click', 'button[data-target="#crearEditarDepartamentoModal"][data-id]', function(){
        var button = $(this);
        var id = button.data('id');
        var nombre = button.data('nombre');
        var modal = $('#crearEditarDepartamentoModal');

        modal.find('#modal-title').text('Editar Departamento');
        modal.find('form').attr('action', '/admin/departamentos/' + id);

        // Agregar método PUT si no existe
        if(modal.find('input[name="_method"]').length === 0){
            modal.find('form').append('<input type="hidden" name="_method" value="PUT">');
        }

        modal.find('input[name="nombre"]').val(nombre);
    });

    // Eliminación con SweetAlert2
    $('.form-eliminar').on('submit', function(e){
        e.preventDefault();
        var form = this;

        Swal.fire({
            title: '¿Está seguro?',
            text: "¡El departamento se eliminará permanentemente!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@stop
