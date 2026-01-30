<!DOCTYPE html>
<html>
<head>
<style>
@page {
    size: 85.6mm 53.98mm;
    margin: 0;
}
.card {
    width: 85.6mm;
    height: 53.98mm;
    position: relative;
    page-break-after: always;
    font-family: Arial, sans-serif;
}
.bg {
    position: absolute;
    width: 100%;
    height: 100%;
}
.element {
    position: absolute;
}
</style>
</head>
<body>

@foreach($empleados as $empleado)
<div class="card">
    <img src="{{ asset('storage/'.$template->background) }}" class="bg">

    @foreach($template->elements as $el)

        @if($el->type === 'text')
        <div class="element"
            style="
                left: {{ $el->x }}px;
                top: {{ $el->y }}px;
                font-size: {{ $el->font_size }}px;
                color: {{ $el->color }};
            ">
            @switch($el->field)
                @case('nombres')
                    {{ $empleado->nombres }}
                    @break
                @case('apellidos')
                    {{ $empleado->apellidos }}
                    @break
                @case('dni')
                    {{ $empleado->dni }}
                    @break
                @case('departamento')
                    {{ $empleado->departamento->nombre ?? '' }}
                    @break
            @endswitch
        </div>
        @endif

        @if($el->type === 'photo')
        <img
            src="{{ asset($empleado->photo ?? 'img/default-user.png') }}"
            class="element"
            style="
                left: {{ $el->x }}px;
                top: {{ $el->y }}px;
                width: 80px;
                height: 100px;
                object-fit: cover;
            ">
        @endif

    @endforeach
</div>
@endforeach

<script>
window.print();
</script>

</body>
</html>

