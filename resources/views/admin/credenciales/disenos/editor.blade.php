@extends('layouts.app')

@section('content')

<h3>Editor Credencial</h3>

<button id="guardar" class="btn btn-success">
Guardar Diseño
</button>

<br><br>

<div id="canvas">

<img id="foto" class="elemento"
src="/img/foto-demo.png">

<div id="nombre" class="elemento texto">
NOMBRE
</div>

<div id="dni" class="elemento texto">
DNI
</div>

<div id="qr" class="elemento">
QR
</div>

</div>

@endsection

@endsection
@section('css')
<style>
    #canvas{

width:1016px;
height:638px;

border:2px solid #ccc;

position:relative;

background-size:cover;

}

.elemento{

position:absolute;
cursor:move;

}

.texto{

font-size:20px;
font-weight:bold;

}
</style>

@endsection

@section('js')
<script>
    var canvas = new fabric.Canvas('canvas');

canvas.setWidth(1016);
canvas.setHeight(638);


// texto dinamico 
document.getElementById('addText').onclick=function(){

var text = new fabric.IText('NOMBRE',{

left:200,
top:200,
fontSize:28,
fill:'black'

});

canvas.add(text);

}

// agregar foto  
document.getElementById('addFoto').onclick=function(){

fabric.Image.fromURL('/img/foto-demo.png',function(img){

img.set({
left:50,
top:80,
scaleX:0.5,
scaleY:0.5
});

canvas.add(img);

});

}

// agregar QR

document.getElementById('addQR').onclick=function(){

fabric.Image.fromURL('/img/qr-demo.png',function(img){

img.set({
left:400,
top:300,
scaleX:0.4,
scaleY:0.4
});

canvas.add(img);

});

}

// guardar diseño 
document.getElementById('guardar').onclick=function(){

var json = JSON.stringify(canvas);

fetch("{{ route('admin.disenos.guardar',$diseno->id) }}",{

method:'POST',

headers:{
'Content-Type':'application/json',
'X-CSRF-TOKEN':'{{ csrf_token() }}'
},

body:JSON.stringify({
json:json
})

});

alert("Diseño guardado");

} 

</script>

<script>

$(".elemento").draggable({
containment:"#canvas"
});

$("#guardar").click(function(){

let config={};

$(".elemento").each(function(){

let id=$(this).attr("id");

config[id]={

x:$(this).position().left,
y:$(this).position().top

};

});

$.post("{{ route('disenos.guardar',$diseno->id) }}",{

_token:"{{ csrf_token() }}",

config:JSON.stringify(config)

},function(){

alert("Diseño guardado");

});

});
</script>
@endsection