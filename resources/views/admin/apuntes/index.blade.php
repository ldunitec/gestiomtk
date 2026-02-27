@extends('adminlte::page')

@section('title', 'Apuntes Seguros')

@section('content_header')
    <h1><i class="fas fa-lock"></i> Apuntes Seguros</h1>
@stop

@section('content')

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Correcto',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.apuntes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Apunte
            </a>
        </div>

        <div class="card-body">
            <table id="tablaApuntes" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>IP / URL</th>
                        <th>Usuario</th>
                        <th>Creado por</th>
                        <th width="180">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($apuntes as $apunte)
                        <tr>
                            <td>{{ $apunte->titulo }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ strtoupper($apunte->tipo) }}
                                </span>
                            </td>
                      
                            <td>
                                {{ $apunte->ip ?? $apunte->url }}
                            </td>
                            <td>{{ $apunte->usuario }}</td>
                            <td>{{ $apunte->creador->name ?? '' }}</td>
                            <td>

                              
                                @role('ADMINISTRADOR')
                                    <button class="btn btn-danger" onclick="mostrarPassword({{ $apunte->id }})">
                                        🔐 Ver Contraseña
                                    </button>
                                @endrole


                                <a href="{{ route('admin.apuntes.edit', $apunte) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="{{ route('admin.apuntes.historial', $apunte->id) }}"
                                    class="btn btn-sm btn-secondary">
                                    <i class="fas fa-history"></i>
                                </a>

                                <form action="{{ route('admin.apuntes.destroy', $apunte) }}" method="POST"
                                    style="display:inline" class="formEliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop

@section('js')

    <script>
        $(function() {

            $('#tablaApuntes').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });

            $('.formEliminar').submit(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar apunte?',
                    text: "No podrás revertir esto",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                })
            });

        });



    function mostrarPassword(id) {
        let url = "{{ route('admin.apuntes.verPassword', ':id') }}";
        url = url.replace(':id', id);

        Swal.fire({
            title: 'Confirmar contraseña',
            input: 'password',
            inputLabel: 'Ingrese su contraseña',
            inputPlaceholder: '••••••••',
            showCancelButton: true,
            // confirmButtonText: 'Ver'
          
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, {
                    _token: '{{ csrf_token() }}',
                    password_confirm: result.value.trim() // trim para eliminar espacios
                }, function(response) {
                    Swal.fire({
                        title: 'Contraseña',
                        text: response.password,
                        icon: 'info'
                    });
                }).fail(function(xhr) {
                    Swal.fire('Error', 'Contraseña incorrecta', 'error');
                });
            }
        });
    }





        function generarPassword(){

    const length = 20;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
    let password = "";

    const values = new Uint32Array(length);
    window.crypto.getRandomValues(values);

    for(let i=0;i<length;i++){
        password += charset[values[i] % charset.length];
    }

    document.getElementById('password').value = password;
}

    </script>



@stop
