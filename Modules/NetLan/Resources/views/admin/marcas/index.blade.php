@extends('adminlte::page')

@section('title', 'Listado de Marcas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold text-dark">Listado de Marcas</h1>
        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#createModal">
            <i class="fas fa-plus-circle mr-1"></i> Nueva Marca
        </button>
    </div>
    <hr class="mt-2 mb-3">
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title text-bold"><i class="fas fa-tags mr-2 text-primary"></i> Marcas Registradas</h3>
                </div>
                
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-hover table-striped table-sm w-100">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">Nro</th>
                                <th>Nombre</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($marcas as $marca)
                                <tr>
                                    <td class="text-center align-middle font-weight-bold text-secondary">{{ $loop->iteration }}</td>
                                    <td class="align-middle">{{ $marca->nombre }}</td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group btn-group-sm" role="group" aria-label="Acciones de Marca">
                                            <button type="button" 
                                                    class="btn btn-outline-success btn-edit" 
                                                    data-toggle="modal"
                                                    data-target="#editModal" 
                                                    data-id="{{ $marca->id }}"
                                                    data-nombre="{{ $marca->nombre }}"
                                                    title="Editar Marca">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>

                                            <form action="{{ route('netlan.admin.marcas.destroy', $marca->id) }}" 
                                                  method="POST" 
                                                  class="d-inline form-eliminar">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Eliminar Marca">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle mr-1"></i> No hay marcas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('netlan::admin.marcas.parciales.modal')
@stop

@section('css')
    @include('netlan::admin.marcas.parciales.css')
@stop

@section('js')
    @include('netlan::admin.marcas.parciales.js')
    
    <script>
        $(document).ready(function() {
            // Delegación de eventos para SweetAlert2 (compatible con paginación/filtrado de DataTables)
            $(document).on('submit', '.form-eliminar', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción eliminará la marca permanentemente!",
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