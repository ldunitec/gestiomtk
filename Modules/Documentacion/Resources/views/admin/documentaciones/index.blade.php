@extends('adminlte::page')

@section('title', 'Listado de Documentos')

@section('content_header')

@stop

@section('content')
  
<a href="{{ route('documentacion.admin.documentaciones.create') }}" class="btn btn-primary">Nueva</a>
<button type="button" onclick="importar('modelo')">Importar Modelo</button>

<div id="editor" style="height:400px;"></div>
<textarea name="contenido" id="contenido" hidden></textarea>

<table class="table">
    <thead>
        <tr>
            <th>Título</th>
            <th>Módulo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($docs as $d)
        <tr>
            <td>{{ $d->titulo }}</td>
            <td>{{ $d->modulo }}</td>
            <td>
                <a href="{{ route('documentacion.admin.documentaciones.edit',$d->id) }}" class="btn btn-warning">Editar</a>
                <a href="{{ route('documentacion.admin.documentaciones..pdf',$d->id) }}" class="btn btn-danger">PDF</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@stop

@section('css')
    <style>
       .small-box {
    height: 180px;              /* altura fija */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;           /* evita que el contenido agrande el card */
}

.small-box .inner {
    flex: 1;
    overflow: hidden;
}

.small-box h3 {
    font-size: 1.1rem;
    white-space: nowrap;        /* evita salto de línea */
    overflow: hidden;
    text-overflow: ellipsis;    /* ... si el texto es largo */
}

.small-box p {
    font-size: 0.85rem;
    margin-bottom: 0;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;      /* máximo 2 líneas */
    -webkit-box-orient: vertical;
}

.small-box .icon {
    position: absolute;
    bottom: 10px;
    right: 10px;
}

.small-box img {
    max-height: 80px;
    max-width: 100%;
    object-fit: contain;
}

.ribbon-wrapper {
    z-index: 10;
}
    </style>

@stop

@section('js')
<script src="https://unpkg.com/monaco-editor@latest/min/vs/loader.js"></script>


<script>
require.config({ paths: { vs: 'https://unpkg.com/monaco-editor@latest/min/vs' }});

require(['vs/editor/editor.main'], function () {
    var editor = monaco.editor.create(document.getElementById('editor'), {
        value: '',
        language: 'php',
        theme: 'vs-dark'
    });

    document.querySelector('form').addEventListener('submit', function () {
        document.getElementById('contenido').value = editor.getValue();
    });
});
</script>
<script>
    function importar(tipo){
    let nombre = prompt("Nombre del archivo");

    fetch('/documentacion/importar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ tipo, nombre })
    })
    .then(res => res.json())
    .then(data => {
        editor.setValue(data.contenido);
    });
}
</script>
@stop


