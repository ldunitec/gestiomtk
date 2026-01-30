{{-- resources/views/admin/notes/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Notas')

@section('content_header')
    <h1>Diseñadors</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">

        <div class="card">
            <div class="card-header">
                <strong>Herramientas</strong>
            </div>

            <div class="card-body">
                <button id="addText" class="btn btn-primary btn-block mb-2">
                    ➕ Agregar Texto
                </button>

                <button id="addPhoto" class="btn btn-secondary btn-block mb-2">
                    🖼 Agregar Foto
                </button>
            </div>
        </div>

    </div>

    <div class="col-md-9">
    <div id="cardCanvas" class="card-canvas">

        <img src="{{ asset('storage/'.$template->background) }}" class="bg">

        @foreach($template->elements as $el)
            @include('admin.templates.partials.element', ['el'=>$el])
        @endforeach

    </div>
</div>
</div>


@stop
 @section('css')
 .card-canvas{
    width:340px;
    height:215px;
    position:relative;
    border:2px dashed #999;
    background:#fff;
}

.bg{
    position:absolute;
    width:100%;
    height:100%;
    z-index:1;
}

.element{
    position:absolute;
    z-index:10;
    cursor:move;
    padding:2px 5px;
    border:1px dashed transparent;
}

.element:hover{
    border-color:#007bff;
}

 @stop


@section('js')
<script>
$('#addText').click(function(){

    axios.post('/admin/template-elements', {
        template_id: {{ $template->id }},
        type: 'text',
        field: 'nombres'
    }).then(res=>{
        $('#cardCanvas').append(res.data.html);
        initDrag();
    });

});
</script>

@stop
