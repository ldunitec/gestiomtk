{{-- resources/views/admin/notes/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Notas')

@section('content_header')
    <h1>Notas</h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#notaModal">
        Nueva Nota
    </button>

@stop

@section('content')
    <div class="card">
        <div class="card-body">

            <form id="formNota">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="titulo" class="form-control" placeholder="Título" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="nota" class="form-control" placeholder="Nota" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Agregar</button>
                    </div>
                </div>
            </form>

            <hr>

            <table id="tablaNotas" class="table table-bordered response">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notas as $nota)
                        <tr>
                            <td>{{ $nota->id }}</td>
                            <td>{{ $nota->descripcion }}</td>
                            <td>
                                <button class="btn btn-info btn-sm verNota" data-id="{{ $nota->id }}">Ver</button>
                                <button class="btn btn-warning btn-sm editarNota"
                                    data-id="{{ $nota->id }}">Editar</button>
                                <a href="{{ route('notas.show', $nota) }}" target="_blank"
                                    class="btn btn-secondary btn-sm">Imprimir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <div class="modal fade" id="notaModal">
        <div class="card text-white bg-info">
            <div class="card-body">
                {{-- <form id="formNota"> --}}
                    <form action="{{ route('admin.notas.store') }}">
                        @method('POST')
                        @csrf
                    <input type="hidden" id="nota_id">
                    <div class="modal-body">
                        <label>Descripción</label>
                        <input type="text" id="descripcion" class="form-control">

                        <label class="mt-2">Contenido</label>
                        <textarea id="contenido" name="contenido" class="form-control"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="modal fade" id="previewModal">
        <div class="modal-body">
            <h4 id="prevDesc"></h4>
            <hr>
            <div id="prevContent"></div>
        </div>
    </div>

@stop



@section('js')
    <script>
        $('#tablaNotas').DataTable();

        $('.editarNota').click(function() {
            let id = $(this).data('id');
            $.get('/notas/' + id + '/edit', function(nota) {
                $('#nota_id').val(nota.id);
                $('#descripcion').val(nota.descripcion);
                $('#contenido').val(nota.contenido);
                $('#notaModal').modal('show');
            });
        });

        $('.verNota').click(function() {
            let id = $(this).data('id');
            $.get('/notas/' + id + '/edit', function(nota) {
                $('#prevDesc').text(nota.descripcion);
                $('#prevContent').html(nota.contenido.replace(/\n/g, '<br>'));
                $('#previewModal').modal('show');
            });
        });
    </script>
    <script>
        function initEditor() {
            tinymce.init({
                selector: '#contenido',
                height: 300,
                menubar: false,
                plugins: 'lists link table code wordcount',
                toolbar: `
            undo redo | formatselect | 
            bold italic underline | forecolor backcolor |
            alignleft aligncenter alignright alignjustify |
            bullist numlist | table | code
        `,
                branding: false
            });
        }

        // cuando abre el modal
        $('#notaModal').on('shown.bs.modal', function() {
            if (!tinymce.get('contenido')) {
                initEditor();
            }
        });

        // cuando cierra el modal
        $('#notaModal').on('hidden.bs.modal', function() {
            tinymce.remove('#contenido');
        });

        $('.editarNota').click(function() {
            let id = $(this).data('id');

            $.get('/notas/' + id + '/edit', function(nota) {
                $('#nota_id').val(nota.id);
                $('#descripcion').val(nota.descripcion);

                $('#notaModal').modal('show');

                setTimeout(() => {
                    tinymce.get('contenido').setContent(nota.contenido);
                }, 300);
            });
        });

        $('#formNota').submit(function(e) {
            e.preventDefault();

            let contenido = tinymce.get('contenido').getContent();

            $.ajax({
                url: '/notas/' + $('#nota_id').val(),
                type: 'PUT',
                data: {
                    _token: $('input[name=_token]').val(),
                    descripcion: $('#descripcion').val(),
                    contenido: contenido
                },
                success: function() {
                    location.reload();
                }
            });
        });

        $('#prevContent').html(nota.contenido);
    </script>

@stop
