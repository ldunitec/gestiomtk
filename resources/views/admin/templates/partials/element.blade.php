@if($el->type === 'text')
<div class="element text"
     data-id="{{ $el->id }}"
     style="
        left: {{ $el->x }}px;
        top: {{ $el->y }}px;
        font-size: {{ $el->font_size }}px;
        color: {{ $el->color }};
     ">
    {{ strtoupper($el->field) }}
</div>
@endif

@if($el->type === 'photo')
<img src="{{ asset('img/photo-placeholder.png') }}"
     class="element photo"
     data-id="{{ $el->id }}"
     style="left:{{ $el->x }}px;top:{{ $el->y }}px;width:80px;">
@endif
