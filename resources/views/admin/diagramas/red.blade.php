@extends('adminlte::page')

@section('content')
<style>
#canvas{height:650px;background:#f4f6f9;border:1px solid #ccc;position:relative}
.node{
    width:170px;height:80px;border-radius:8px;
    position:absolute;color:#fff;text-align:center;
    cursor:move;font-size:13px;padding-top:6px
}
.router{background:#007bff}
.switch{background:#28a745}
.ap{background:#6f42c1}
.pc{background:#6c757d}
</style>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Diagrama de Red</h3>
    </div>
    <div class="card-body">
        <div id="canvas">
            @foreach($equipos as $e)
                <div class="node {{ $e->tipo ?? 'pc' }}" id="eq{{ $e->id }}"
                    style="left:{{ $e->x }};top:{{ $e->y }}">
                    <b>{{ $e->nombre }}</b><br>
                    IP: {{ $e->ip_address }}<br>
                    MAC: {{ $e->mac_addrres }}
                </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jsPlumb/2.15.6/js/jsplumb.min.js"></script>
<script>
jsPlumb.ready(()=>{
    jsPlumb.setContainer("canvas");

    @foreach($equipos as $e)
        jsPlumb.draggable("eq{{ $e->id }}");
        jsPlumb.makeSource("eq{{ $e->id }}",{anchor:"Continuous"});
        jsPlumb.makeTarget("eq{{ $e->id }}",{anchor:"Continuous"});
    @endforeach

    jsPlumb.bind("dragStop", info=>{
        fetch("{{ url('admin/diagramas/red/posicion') }}",{
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                'Content-Type':'application/json'
            },
            body:JSON.stringify({
                id: info.el.id.replace('eq',''),
                x: info.el.style.left,
                y: info.el.style.top
            })
        });
    });
});
</script>
@endsection
