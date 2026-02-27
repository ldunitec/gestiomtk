<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
.card-print {
    width: 85.6mm;
    height: 53.98mm;
    position: relative;
    page-break-after: always;
}
.bg {
    position:absolute;
    width:100%;
    height:100%;
    object-fit:cover;
}


</style>
</head>
<body>

{{-- <div class="card-print">
@foreach($design->layout as $el)
    @if($el['type']=='text')
        <div style="
            position:absolute;
            left:{{ $el['x'] }};
            top:{{ $el['y'] }};
            font-size:{{ $el['size'] }};
            color:{{ $el['color'] }};
            text-align:{{ $el['align'] }};
        ">
            {{ $persona->nombre }}
        </div>
    @else
        <img src="{{ $persona->foto }}" style="
            position:absolute;
            left:{{ $el['x'] }};
            top:{{ $el['y'] }};
            width:70px;
        ">
    @endif
@endforeach
</div> --}}

<div class="card-print front">
    <img src="{{ $design->layout['backgrounds']['front'] }}" class="bg">
    @foreach($design->layout['front'] as $el)
        @include('admin.idcards.element', ['el'=>$el])
    @endforeach
</div>

<div class="card-print back">
    <img src="{{ $design->layout['backgrounds']['back'] }}" class="bg">
    @foreach($design->layout['back'] as $el)
        @include('admin.idcards.element', ['el'=>$el])
    @endforeach
</div>


</body>
</html>
