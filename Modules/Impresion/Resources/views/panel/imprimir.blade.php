<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Imprimir fotos por curso y sección</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body { font-family: Arial, sans-serif; margin: 12px; color:#222; }
        .controles { background:#edf3f9; padding:16px; margin-bottom:16px; border-radius:8px; }
        button,a { padding:10px; margin:4px; display:inline-block; }
        .fotos { display:flex; flex-wrap:wrap; align-items:flex-start; gap:5mm; }
        figure { margin:0; width:{{ $previa['ancho'] }}mm; break-inside:avoid; page-break-inside:avoid; }
        img { display:block; width:{{ $previa['ancho'] }}mm; height:{{ $previa['alto'] }}mm; object-fit:contain; }
        figcaption { font-size:9px; overflow-wrap:anywhere; text-align:center; padding:2mm 0; }
        .error { background:#f8d7da; padding:12px; }
        @media print { .controles,.error { display:none!important; } body { margin:0; } }
    </style>
</head>
<body>
@if($errors->any())<div class="error">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<div class="controles">
    <h1>Impresión de fotos</h1>
    <p>{{ $alumnos->count() }} fotos · {{ $previa['ancho'] }} × {{ $previa['alto'] }} mm.
    {{ $previa['faltantes'] }} registros sin foto quedaron fuera del lote.</p>
    <p>Imprime a escala 100%. Después de completar la impresión, confírmala para actualizar el estado.</p>
    <button type="button" onclick="window.print()">1. Abrir impresión</button>
    <form method="POST" action="{{ route('impresion.confirmar', $token) }}" style="display:inline">
        @csrf<button type="submit" onclick="return confirm('¿Terminaste de imprimir estas fotos? Se marcarán como impresas.')">2. Confirmar impresión realizada</button>
    </form>
    <a href="{{ route('impresion.panel') }}">Volver al panel</a>
    <p>Cancelar el diálogo o cerrar esta vista no marca ninguna impresión.</p>
</div>
<div class="fotos">
@foreach($alumnos as $alumno)
    <figure>
        <img src="{{ route('impresion.foto', $alumno->id) }}" alt="Foto de {{ $alumno->nombres }} {{ $alumno->apellidos }}">
        <figcaption><strong>{{ mb_strtoupper($alumno->nombres.' '.$alumno->apellidos) }}</strong><br>{{ $alumno->curso }} · {{ $alumno->seccion }} · {{ $alumno->modalidad }}</figcaption>
    </figure>
@endforeach
</div>
</body></html>
