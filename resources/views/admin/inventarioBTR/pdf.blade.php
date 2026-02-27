<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Inventario</title>

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
            border: 1px solid #000;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
        }

        .col {
            border: 1px solid #000;
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
            border: 1px solid #000;
            text-align: center;
            padding: 10px;
        }

        .imagen img {
            max-width: 100%;
            max-height: 350px;
        }

        h3 {
            text-align: center;
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <!-- ENCABEZADO -->
    <table>
        <tr>
            <td class="header">
                REPORTE DE INVENTARIO
            </td>
        </tr>
    </table>

    <br>

    <!-- DOS COLUMNAS -->
    <table>
        <tr>
            <!-- COLUMNA 1 -->
            <td class="col">
                 <h3>Datos de Comunicación y Ubicación</h3>
                <hr>
                 <p><b>Localidad:</b> {{ $registro->localidad }}</p>
                <p><b>IP Address:</b> {{ $registro->ip }}</p>
                <p><b>Fases:</b> {{ $registro->fases }}</p>
                <p><b>RC-308:</b> {{ $registro->rc }}</p>
                <p><b>Fibra Activa:</b> {{ $registro->fibra }}</p>
                <p><b>Ping:</b> {{ $registro->ping }}</p>
            </td>

            <!-- COLUMNA 2 -->
            <td class="col">
                <h3>Accesorios para cámara</h3>
                <hr>
                <p><b>Cámaras:</b> {{ $registro->camaras }}</p>
                <p><b>Fuente:</b> {{ $registro->fuente }}</p>
                <p><b>Switch:</b> {{ $registro->switchPoE }}</p>
                <p><b>X-Com:</b> {{ $registro->xcom }}</p>
                <p><b>SG-116:</b> {{ $registro->sg116 }}</p>
                <p><b>Observaciones:</b> {{ $registro->observaciones }}</p>
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

</body>
</html>
