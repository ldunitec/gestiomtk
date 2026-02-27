@extends('adminlte::page')

@section('content_header')
<h1>Preview Carnet</h1>
@stop

@section('content')

<div class="card-preview">

    {{-- Fondo --}}
    @if($design->front_bg)
        <div class="bg"
             style="background-image:url('{{ asset('storage/'.$design->front_bg) }}')">
        </div>
    @endif

    {{-- Campos --}}
    @foreach($design->front_layout as $field => $pos)

        @if($field === 'foto' && $persona->foto)
            <img src="{{ asset('storage/'.$persona->foto) }}"
                 class="photo"
                 style="left:{{ $pos['x'] }}px; top:{{ $pos['y'] }}px;">
        @elseif(isset($persona->$field))
            <div class="field"
                 style="left:{{ $pos['x'] }}px;
                        top:{{ $pos['y'] }}px;
                        font-size:{{ $pos['size'] }}px;">
                {{ $persona->$field }}
            </div>
        @endif

    @endforeach
</div>

<a href="{{ route('admin.idcards.print',[$persona->id,$design->id]) }}"
   class="btn btn-primary mt-3" target="_blank">
   Imprimir
</a>

@stop

@section('css')
<style>
.card-preview {
    width: 1013px;
    height: 638px;
    position: relative;
    border: 1px solid #999;
}

.bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    z-index: 0;
}

.field, .photo {
    position: absolute;
    z-index: 2;
}

.photo {
    width: 200px;
    height: 260px;
    object-fit: cover;
}
</style>
@stop
