@extends('adminlte::page')

@section('content_header')

    <h1><b>Listado de equipos </b></h1>
    <hr>
@stop

@section('content')
    @include('admin.equipos.parciales.modal')


    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Equipos Registrados</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearEquipoModal">
                            Nuevo equipo
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-hover table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Nro</th>
                                    <th style="text-align: center">Asignado</th>
                                    <th style="text-align: center">Departamento</th>
                                    <th style="text-align: center">Nombre del equipo</th>
                                    <th style="text-align: center">Marca</th>
                                    <th style="text-align: center">Ip_Address</th>
                                    <th style="text-align: center">Mac_Address</th>
                                    <th style="text-align: center">Tipo</th>
                                    <th style="text-align: center">Antivirus</th>
                                    <th style="text-align: center">Estado</th>
                                    <th style="text-align: center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach ($equipos as $equipo)
                                    {{-- <pre>{{ dd($equipo->empleado) }}</pre> --}}
                                    {{-- {{ optional($equipo->empleado)->nombres . ' ' . optional($equipo->empleado)->apellidos ?? 'Sin empleado' }} --}}


                                    <tr>
                                        <td style="text-align: center">{{ $contador++ }}</td>

                                        <td>
                                            {{ $equipo->empleado ? $equipo->empleado->nombres . ' ' . $equipo->empleado->apellidos : 'Sin empleado' }}
                                        </td>
                                        <td>
                                            @if ($equipo->empleado && $equipo->empleado->departamento)
                                                {{ $equipo->empleado->departamento->nombre }}
                                            @else
                                                Sin Depto
                                            @endif
                                        </td>

                                        <td> {{ $equipo->nombre }}</td>
                                        <td>{{ optional($equipo->marca)->nombre ?? 'Sin marca' }}</td>

                                        <td>{{ $equipo->ip_address }}</td>
                                        <td>{{ $equipo->mac_address }}</td>
                                        <td>{{ $equipo->puerto_patch }}</td>
                                        <td>{{ $equipo->antivirus }}</td>
                                        <td>
                                            <a href="javascript:void(0)"
                                                class="btn btn-sm toggle-estado {{ $equipo->estado == 'Activo' ? 'btn-success' : 'btn-danger' }}"
                                                data-id="{{ $equipo->id }}">
                                                {{-- <i class="fas fa-desktop"></i> - --}}
                                                {{ $equipo->estado == 'Activo' ? 'Act' : 'InAct' }}
                                            </a>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <a href="{{ url('/admin/equipos/' . $equipo->id . '/edit') }}"
                                                    class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>


                                                <form action="{{ url('/admin/equipos', $equipo->id) }}" method="post"
                                                    onclick="preguntar{{ $equipo->id }}(event)"
                                                    id="miFormulario{{ $equipo->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>


                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>



@stop

@section('css')

    @include('admin/equipos/parciales/css')

@section('js')

    @include('admin/equipos/parciales/js')
@stop
