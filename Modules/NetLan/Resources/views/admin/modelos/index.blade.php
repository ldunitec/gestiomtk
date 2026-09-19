@extends('adminlte::page')

@section('title', 'Listado de Modelos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold text-dark">Listado de Modelos</h1>
        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#createModal">
            <i class="fas fa-plus-circle mr-1"></i> Nuevo Modelo
        </button>
    </div>
    <hr class="mt-2 mb-3">
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header border-bottom-0">
                <h3 class="card-title text-bold">
                    <i class="fas fa-cubes mr-2 text-primary"></i> Modelos Registrados
                </h3>
            </div>
            
            <div class="card-body">
                <table id="example1" class="table table-bordered table-hover table-striped table-sm w-100">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">Nro</th>
                            <th>Marca</th>
                            <th>Nombre</th>
                            <th class="text-center" style="width: 140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($modelos as $modelo)
                            <tr>
                                <td class="text-center align-middle font-weight-bold text-secondary">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="align-middle">
                                    {{-- <span class="badge badge-light border"> --}}
                                    <span class="align-middle font-weight-bold">
                                        {{ $modelo->marca?->nombre ?? 'Sin Marca' }}
                                    </span>
                                </td>
                                <td class="align-middle font-weight-bold">
                                    {{ $modelo->nombre }}
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Acciones del Modelo">
                                        <button type="button" 
                                                class="btn btn-outline-success btn-edit" 
                                                data-toggle="modal"
                                                data-target="#editModal" 
                                                data-id="{{ $modelo->id }}"
                                                data-nombre="{{ $modelo->nombre }}" 
                                                data-marca_id="{{ $modelo->marca_id }}"
                                                title="Editar Modelo">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>

                                        <form action="{{ route('netlan.admin.modelos.destroy', $modelo->id) }}" 
                                              method="POST" 
                                              class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar Modelo">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mr-1"></i> No hay modelos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('netlan::admin.modelos.parciales.modal')
@stop

@section('css')
    @include('netlan::admin.modelos.parciales.css')
@stop

@section('js')
    @include('netlan::admin.modelos.parciales.js')
    
    <script>
        $(document).ready(function() {
            // Confirmación genérica de eliminación con SweetAlert2
            $(document).on('submit', '.form-eliminar', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción eliminará el modelo permanentemente!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmColor: '#d33',
                    cancelColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop