<!DOCTYPE html>
<html>

<head>

<style>

.card{

/* width:85.60mm;
width:85.60mm; */
width:53.98mm;
height:85.60mm;
border:1px solid black;
padding:5mm;

}

.foto{

width:25mm;
height:30mm;

}

.page-break{
page-break-after:always;
}

</style>

</head>

<body>

<!-- FRENTE -->

<div class="card">

<img src="{{ public_path('fotos/'.$credencial->foto) }}" class="foto">

<h3>{{ $credencial->nombre }}</h3>

<p>DNI {{ $credencial->dni }}</p>

{!! QrCode::size(70)->generate($credencial->codigo) !!}

</div>

<div class="page-break"></div>

<!-- REVERSO -->

<div class="card">

<h4>IDENTIFICACIÓN</h4>

<p>Este carnet es personal</p>

<p>No transferible</p>

<p>www.institucion.com</p>

</div>

</body>

</html>