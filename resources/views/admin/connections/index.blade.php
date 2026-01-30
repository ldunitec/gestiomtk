@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de conexiones </b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Conexiones registradas</h3>
                    <hr>
                    <h4 style="background-color: red; color: white;">Colocar color en vista de conexiones segun el tipo de
                        equipo eje pc=azul Cam=rojo Tel = amarillo revizar =naranaja directos=verde</h4>
                    <h4>mensaje de error</h4>
                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <a href="{{ url('admin/connections/show') }}" class="btn btn-warning">ver</a>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearPortsModal">
                            Nueva
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
                                <th style="text-align: center">Tipo</th>
                                <th style="text-align: center">Asignado</th>
                                <th style="text-align: center">Equipo</th>
                                <th style="text-align: center">Dir. IP</th>
                                <th style="text-align: center">Mac add</th>
                                <th style="text-align: center">Patch panel</th>
                                <th style="text-align: center">Puerto SW</th>
                                <th style="text-align: center">Comentario</th>
                                <th style="text-align: center">Actualizado</th>
                                <th style="text-align: center">Estado</th>
                                <th style="text-align: center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @foreach ($conexiones as $conexion)
                                <tr>
                                    <td style="text-align: center">{{ $contador++ }}</td>
                                    <td>{{ $conexion->equipo->puerto_patch }}</td>
                                    <td>{{ $conexion->equipo->empleado->nombres . ' ' . $conexion->equipo->empleado->apellidos }}
                                    </td>
                                    <td>{{ $conexion->equipo->nombre }}</td>
                                    <td>{{ $conexion->equipo->ip_address }}</td>
                                    <td>{{ $conexion->equipo->mac_address }}</td>
                                    <td>{{ $conexion->nombre }} </td>
                                    <td>{{ $conexion->sw }} </td>
                                    <td>{{ $conexion->comentario }} </td>
                                    <td>{{ $conexion->updated_at }}</td>
                                    <td>
                                        <a href="javascript:void(0)"
                                            class="btn btn-sm toggle-estado
                                                @if ($conexion->equipo->puerto_patch == 'PC') 
                                                {{ $conexion->estado == 'Activo' ? 'btn-primary' : 'btn-secondary' }}"
                                                  @else                                                    
                                                {{ $conexion->estado == 'Activo' ? 'btn-success' : 'btn-secondary' }}"
                                                 @endif
                                                

                                                data-id="{{ $conexion->id }}">
                                            <i class="fas fa-desktop"></i> -
                                            {{ $conexion->estado == 'Activo' ? 'Act' : 'InAct' }}
                                        </a>
                                    </td>


                                    <td style="text-align: center">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a href="{{ url('/admin/connections/' . $conexion->id . '/edit') }}"
                                                class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                            <form action="{{ url('/admin/connections', $conexion->id) }}" method="post"
                                                onclick="preguntar{{ $conexion->id }}(event)"
                                                id="miFormulario{{ $conexion->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>

                                        </div>
                                        <script>
                                            function preguntar{{ $conexion->id }}(event) {
                                                event.preventDefault();
                                                Swal.fire({
                                                    title: '¿Desea eliminar esta registro?',
                                                    text: '',
                                                    icon: 'question',
                                                    showDenyButton: true,
                                                    confirmButtonText: 'Eliminar',
                                                    confirmButtonColor: '#a5161d',
                                                    denyButtonColor: '#270a0a',
                                                    denyButtonText: 'Cancelar',
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        var form = $('#miFormulario{{ $conexion->id }}');
                                                        form.submit();
                                                    }
                                                });
                                            }
                                        </script>

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

    @include('admin.connections.parciales.modal');

    </div>
    </div>


@stop

@section('css')
    @include('admin.connections.parciales.css')
@stop

@section('js')

    @include('admin.connections.parciales.js')

@stop
