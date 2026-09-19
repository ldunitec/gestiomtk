@section('content_header')

@stop

@section('content')
  
{{-- <h3>{{ $doc->titulo }}</h3> --}}

<form method="POST" action="{{ route('documentacion.admin.documentaciones.store') }}">
@csrf

<select name="tipo" class="form-control">
    <option value="requisitos">Requisitos</option>
    <option value="modelo">Modelo</option>
    <option value="controlador">Controlador</option>
    <option value="migracion">Migración</option>
    <option value="rutas">Rutas</option>
    <option value="vistas">Vistas</option>
    <option value="comandos">Comandos</option>
</select>

<input type="text" name="titulo" class="form-control" placeholder="Título">

<textarea name="contenido" class="form-control" rows="6"></textarea>

<button class="btn btn-success mt-2">Agregar</button>
</form>

<hr>

{{-- @foreach($doc->secciones as $sec)
    <h4>{{ $sec->titulo }} ({{ $sec->tipo }})</h4>
    <div>{!! $sec->contenido !!}</div>
@endforeach --}}

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
