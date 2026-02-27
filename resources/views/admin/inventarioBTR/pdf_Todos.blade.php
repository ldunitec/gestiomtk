<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventario Completo</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header {
            border: 2px solid #000;
        }

        .header td {
            padding: 8px;
            vertical-align: middle;
        }

        .titulo {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .col {
            border: 2px solid #000;
            vertical-align: top;
            padding: 10px;
            width: 50%;
        }

        .col p {
            margin: 4px 0;
        }

        .col b {
            display: inline-block;
            width: 120px;
        }

        .imagen {
            border: 2px solid #000;
            text-align: center;
            padding: 10px;
        }

        .imagen img {
            max-width: 100%;
            max-height: 320px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach($registros as $registro)

    <!-- ENCABEZADO -->
    <table class="header">
        <tr>
            <td width="20%">
                <img src="{{ public_path('img/logo.png') }}" height="50">
            </td>
            <td width="60%" class="titulo">
                REPORTE DE INVENTARIO
            </td>
            <td width="20%" align="center">
                {!! QrCode::size(60)->generate($registro->id) !!}
            </td>
        </tr>
    </table>

    <br>

    <!-- DOS COLUMNAS -->
    <table>
        <tr>
            <!-- COLUMNA 1 -->
            <td class="col">
                <p><b>Localidad:</b> {{ $registro->localidad }}</p>
                <p><b>IP:</b> {{ $registro->ip }}</p>
                <p><b>Fases:</b> {{ $registro->fases }}</p>
                <p><b>RC-308:</b> {{ $registro->rc }}</p>
                <p><b>Fibra:</b> {{ $registro->fibra }}</p>
                <p><b>Ping:</b> {{ $registro->ping }}</p>
            </td>

            <!-- COLUMNA 2 -->
            <td class="col">
                <p><b>Cámaras:</b> {{ $registro->camaras }}</p>
                <p><b>Fuente:</b> {{ $registro->fuente }}</p>
                <p><b>Switch:</b> {{ $registro->switchPoE }}</p>
                <p><b>X-Com:</b> {{ $registro->xcom }}</p>
                <p><b>SG-116:</b> {{ $registro->sg116 }}</p>
                <p><b>Obs:</b> {{ $registro->observaciones }}</p>
            </td>
        </tr>
    </table>

    <br>

    <!-- IMAGEN -->
    <table>
        <tr>
            <td class="imagen">
                @if($registro->imagen)
                    <img src="{{ public_path('storage/' . $registro->imagen) }}">
                @else
                    <p>No hay imagen</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- SALTO DE PÁGINA -->
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

@endforeach

</body>
</html>
