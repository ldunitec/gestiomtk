@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <table class="table table-bordered" id="tablaUsuarios">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        @foreach ($u->roles as $rol)
                            <span class="badge badge-info">{{ $rol->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm btnAsignarRol" data-id="{{ $u->id }}"
                            data-roles='@json($u->roles->pluck('name'))'>
                            Asignar Rol
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- modal 

<div class="modal fade" id="modalRol">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Asignar Roles</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="user_id">

                @foreach ($roles as $rol)
                <div class="form-check">
                    <input type="radio" 
                           class="form-check-input rolRadio"
                           value="{{ $rol->name }}"
                           id="rol_{{ $rol->id }}">
                    <label class="form-check-label">
                        {{ $rol->name }}
                    </label>
                </div>
                @endforeach
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="guardarRol">Guardar</button>
            </div>
        </div>
    </div>
</div> --}}
    <div class="modal fade" id="modalRol">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Asignar Rol</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="user_id">

                    @foreach ($roles as $rol)
                        <div class="form-check">
                            <input type="radio" name="rol" class="form-check-input rolRadio"
                                value="{{ $rol->name }}" id="rol_{{ $rol->id }}">
                            <label class="form-check-label">
                                {{ $rol->name }}
                            </label>
                        </div>
                    @endforeach

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" id="guardarRol">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>


@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')

{{-- con checkbox  --}}
    {{-- <script>
        $(document).ready(function() {

            $('#tablaUsuarios').DataTable();

            // Abrir modal
            $(document).on('click', '.btnAsignarRol', function() {

                let userId = $(this).data('id');
                let roles = $(this).data('roles');

                $('#user_id').val(userId);

                // Limpiar checks
                $('.rolCheck').prop('checked', false);

                // Marcar roles actuales
                roles.forEach(function(rol) {
                    $('.rolCheck[value="' + rol + '"]').prop('checked', true);
                });

                $('#modalRol').modal('show');
            });

            // Guardar roles
            $('#guardarRol').click(function() {

                let user_id = $('#user_id').val();
                let roles = [];

                $('.rolCheck:checked').each(function() {
                    roles.push($(this).val());
                });

                $.ajax({
                    url: "{{ route('admin.usuarios.asignarRol') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        user_id: user_id,
                        roles: roles
                    },
                    success: function(resp) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Correcto',
                            text: resp.message
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            });

        });
    </script> --}}

    {{-- con radiobutton --}}
    <script>
        $(document).ready(function(){

    $('#tablaUsuarios').DataTable();

    // Abrir modal
    $(document).on('click', '.btnAsignarRol', function(){

        let userId = $(this).data('id');
        let rolActual = $(this).data('rol');

        $('#user_id').val(userId);

        // Limpiar radios
        $('.rolRadio').prop('checked', false);

        // Marcar rol actual
        if(rolActual){
            $('.rolRadio[value="'+rolActual+'"]').prop('checked', true);
        }

        $('#modalRol').modal('show');
    });

    // Guardar rol
    $('#guardarRol').click(function(){

        let user_id = $('#user_id').val();
        let rol = $('input[name="rol"]:checked').val();

        if(!rol){
            Swal.fire('Error','Debe seleccionar un rol','warning');
            return;
        }

        $.ajax({
            url: "{{ route('admin.usuarios.asignarRol') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                user_id: user_id,
                rol: rol
            },
            success: function(resp){

                Swal.fire({
                    icon: 'success',
                    title: 'Correcto',
                    text: resp.message
                }).then(()=>{
                    location.reload();
                });
            }
        });
    });

});

    </script>

@stop
