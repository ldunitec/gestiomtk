@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Permisos </b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Permisos registrados</h3>
                    <!-- /.card-tools -->
                    <div class="card-tools">
                        <form action="{{ route('admin.generate-permissions') }}" method="GET">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                Actualizar permisos 
                            </button>
                        </form>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.roles.actualizarPermisos', $rol->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            @foreach ($permisos as $grupo => $lista)
                                <div class="col-md-4">
                                    <div class="card card-info card-collapsed">
                                        <div class="card-header">
                                            <h3 class="card-title"> <input type="checkbox" class="check-grupo"
                                                    data-grupo="{{ $grupo }}"> -
                                                <strong class="strong-group">{{ ucfirst($grupo) }}</strong>
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body" style="display: none">
                                            <ul style="list-style: none; padding-left: 0;">
                                                @foreach ($lista as $permiso)
                                                    <li>
                                                        <label>
                                                            <input type="checkbox" name="permisos[]"
                                                                class="permiso-checkbox permiso-{{ $grupo }}"
                                                                value="{{ $permiso->id }}"
                                                                {{ in_array($permiso->id, $permisosSeleccionados) ? 'checked' : '' }}>
                                                            {{ $permiso->name }}
                                                        </label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>
                                <!-- /.card -->
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-success">Guardar permisos</button>
                    </form>

                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Fondo transparente y sin borde en el contenedor */
        #example1_wrapper .dt-buttons {
            background-color: transparent;
            box-shadow: none;
            border: none;
            display: flex;
            justify-content: center;
            /* Centrar los botones */
            gap: 3px;
            /* Espaciado entre botones */
            margin-bottom: 15px;
            /* Separar botones de la tabla */
        }

        /* Estilo personalizado para los botones */
        #example1_wrapper .btn {
            color: #fff;
            /* Color del texto en blanco */
            border-radius: 4px;
            /* Bordes redondeados */
            padding: 5px 15px;
            /* Espaciado interno */
            font-size: 14px;
            /* Tamaño de fuente */
        }

        /* Colores por tipo de botón */
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-info {
            background-color: #17a2b8;
            border: none;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
            border: none;
        }

        .btn-default {
            background-color: #6e7176;
            color: #212529;
            border: none;
        }

        .card-header {
            background-color: #11eeee;
            color: #fff;
        }

        .strong-group {
            font-weight: bold;
            color: #505157;
            margin-left: 5px;
        }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cuando se cambia el checkbox de grupo
            document.querySelectorAll('.check-grupo').forEach(function(grupoCheckbox) {
                grupoCheckbox.addEventListener('change', function() {
                    const grupo = this.dataset.grupo;
                    const checkboxes = document.querySelectorAll('.permiso-' + grupo);
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            });
        });
    </script>

@stop
