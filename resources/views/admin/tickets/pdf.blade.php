<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Técnico</title>

    <style>
        body{ font-family: Arial; font-size:12px; }
        .header{ text-align:center; }
        .box{ border:1px solid #000; padding:10px; margin-bottom:10px; }
        .titulo{ font-weight:bold; margin-bottom:5px; }
        img{ width:120px; margin:5px; }
    </style>
</head>

<body>

<div class="header">
    <h2>REPORTE TÉCNICO</h2>
<img src="{{ asset('img/logo.png') }}" width="120">    <p>Código: {{ $ticket->codigo }}</p>
</div>

<div class="box">
    <div class="titulo">DATOS GENERALES</div>
    Cliente: {{ $ticket->cliente }} <br>
    Empresa: {{ $ticket->empresa }} <br>
    Equipo: {{ $ticket->equipo }} <br>
    Prioridad: {{ $ticket->prioridad }} <br>
    Estado: {{ $ticket->estado }} <br>
</div>

<div class="box">
    <div class="titulo">PROBLEMA REPORTADO</div>
    {{ $ticket->problema }}
</div>
<div class="box">
    <div class="titulo">SOLUCIÓN APLICADA</div>
    {{ $ticket->solucion }}
</div>

<div class="box">
    <div class="titulo">TÉCNICO ASIGNADO</div>
    {{ $ticket->asignacion->tecnico->name ?? 'N/A' }}
</div>

<div class="box">
    <div class="titulo">EVIDENCIAS</div>

    @foreach($ticket->evidencias as $e)
        @if(in_array($e->tipo,['jpg','jpeg','png']))
            <img src="{{ public_path('evidencias/'.$e->archivo) }}">
        @else
            <p>{{ $e->archivo }}</p>
        @endif
    @endforeach

</div>

<div class="box">
    <div class="titulo">FECHAS</div>
    Creación: {{ $ticket->created_at }} <br>
    Cierre: {{ $ticket->fecha_cierre }}
</div>

<br><br>

<table width="100%">
    <tr>
        <td style="text-align:center;">
            ___________________________<br>
            Firma Técnico
        </td>
        <td style="text-align:center;">
            ___________________________<br>
            Firma Cliente
        </td>
    </tr>
</table>

</body>
</html>