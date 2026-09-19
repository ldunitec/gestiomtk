@extends('adminlte::page')

@section('title', 'Departamentos')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-building text-primary mr-2"></i>Gestión de Departamentos
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Departamentos</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow-sm">
                
                <!-- Card Header -->
                <div class="card-header d-flex align-items-center justify-content-between p-3">
                    <h3 class="card-title font-weight-bold m-0">
                        <i class="fas fa-list mr-1"></i> Departamentos registrados
                        <span class="badge badge-pill badge-primary ml-1">{{ count($departamentos) }}</span>
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" 
                                data-toggle="modal" data-target="#crearEditarDepartamentoModal">
                            <i class="fas fa-plus mr-1"></i> Nuevo departamento
                        </button>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <table id="tabla-departamentos" class="table table-hover table-striped table-bordered dt-responsive nowrap w-100">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 80px;" class="text-center">#</th>
                                <th>Nombre del Departamento</th>
                                <th style="width: 120px;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departamentos as $index => $departamento)
                            <tr>
                                <td class="text-center align-middle font-weight-bold text-secondary">
                                    {{ $index + 1 }}
                                </td>
                                <td class="align-middle font-weight-bold text-dark">
                                    {{ $departamento->nombre }}
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group" role="group" aria-label="Acciones">
                                        <!-- Botón Editar -->
                                        <button class="btn btn-outline-warning btn-sm btn-editar" 
                                                data-toggle="modal" 
                                                data-target="#crearEditarDepartamentoModal"
                                                data-id="{{ $departamento->id }}"
                                                data-nombre="{{ $departamento->nombre }}"
                                                title="Editar departamento">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>

                                        <!-- Formulario Eliminar -->
                                        <form action="{{ url('/admin/departamentos', $departamento->id) }}" 
                                              method="POST" 
                                              class="form-eliminar d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-right" title="Eliminar departamento">
                                                <i class="fas fa-trash-alt"></i>
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
</div>

<!-- Modal Único: Crear / Editar Departamento -->
<div class="modal fade" id="crearEditarDepartamentoModal" tabindex="-1" role="dialog" aria-labelledby="crearEditarDepartamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <form id="form-departamento" method="POST" action="{{ url('/admin/departamentos') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modal-title">
                        <i class="fas fa-plus-circle mr-1"></i> Nuevo Departamento
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <div class="form-group">
                        <label for="nombre" class="font-weight-normal text-dark">Nombre del Departamento <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                            </div>
                            <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Ej. Recursos Humanos" required autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" id="btn-submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card-outline.card-primary {
        border-top: 3px solid #007bff;
    }
    .table td, .table th {
        vertical-align: middle !important;
    }
    .modal-header .close {
        outline: none;
    }
</style>
@stop

@section('js')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // Inicializar DataTables con localización en español
    var table = $('#tabla-departamentos').DataTable({
        responsive: true,
        autoWidth: false,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        columnDefs: [
            { orderable: false, targets: 2 } // Deshabilitar ordenación en la columna de Acciones
        ]
    });

    // Resetear modal para CREAR al presionar "Nuevo departamento"
    $('[data-target="#crearEditarDepartamentoModal"]').not('.btn-editar').on('click', function(){
        var modal = $('#crearEditarDepartamentoModal');
        modal.find('#modal-title').html('<i class="fas fa-plus-circle mr-1"></i> Nuevo Departamento');
        modal.find('form').attr('action', '{{ url("/admin/departamentos") }}');
        modal.find('input[name="nombre"]').val('');
        modal.find('input[name="_method"]').remove();
    });

    // Abrir modal para EDITAR (Usando delegación para DataTables paginado)
    $('#tabla-departamentos').on('click', '.btn-editar', function(){
        var button = $(this);
        var id = button.data('id');
        var nombre = button.data('nombre');
        var modal = $('#crearEditarDepartamentoModal');

        modal.find('#modal-title').html('<i class="fas fa-pencil-alt mr-1"></i> Editar Departamento');
        modal.find('form').attr('action', '{{ url("/admin/departamentos") }}' + '/' + id);

        if(modal.find('input[name="_method"]').length === 0){
            modal.find('form').append('<input type="hidden" name="_method" value="PUT">');
        }

        modal.find('input[name="nombre"]').val(nombre);
    });

    // Eliminación con SweetAlert2 (Delegado para funcionar en cualquier página de DataTables)
    $(document).on('submit', '.form-eliminar', function(e){
        e.preventDefault();
        var form = this;

        Swal.fire({
            title: '¿Confirmar eliminación?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-danger mr-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@stop