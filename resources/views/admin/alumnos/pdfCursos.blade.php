<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Reporte de Alumnos - {{ $curso }}</title>
    <style>
        /* Configuración de la página */
        @page {
            margin: 120px 50px 80px 50px;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Encabezado Fijo */
        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 90px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .logo-container {
            float: left;
            width: 20%;
        }

        .logo-container img {
            max-width: 40px;
            height: auto;
        }

        .header-info {
            float: left;
            width: 60%;
            text-align: center;
        }

        .header-info h1 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
        }

        .header-info p {
            margin: 2px 0;
            color: #7f8c8d;
        }

        /* Títulos de sección */
        .seccion-detalles {
            margin-bottom: 10px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        /* Tabla de Registros */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Ayuda a controlar anchos de columna */
        }

        th {
            background-color: #2c3e50;
            color: white;
            text-transform: uppercase;
            font-size: 10px;
            padding: 10px 5px;
            border: 1px solid #2c3e50;
        }

        td {
            padding: 8px 5px;
            border: 1px solid #bdc3c7;
            vertical-align: middle;
            word-wrap: break-word;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Foto del alumno */
        .foto-perfil {
            width: 45px;
            height: 55px;
            display: block;
            margin: 0 auto;
            border: 1px solid #ddd;
            object-fit: cover;
        }

        .no-foto {
            font-size: 8px;
            color: #999;
            text-align: center;
        }

        /* Contador al final */
        .footer-summary {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .contador-box {
            float: right;
            background: #2c3e50;
            color: white;
            padding: 10px 20px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .contador-box {
            float: right;
            background: #2c3e50;
            /* Color oscuro de fondo */
            color: white;
            /* Color de texto general */
            padding: 15px;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            min-width: 180px;
            font-size: 10px;
        }

        .texto-rojo {
            color: #ff4d4d;
            /* Un rojo vibrante que resalte sobre el fondo oscuro */
            font-weight: bold;
            font-size: 12px;
        }

        .texto-total {
            border-top: 1px solid #555;
            margin-top: 5px;
            padding-top: 5px;
            display: block;
            font-size: 12px;
        }
        .foto-perfil {
    width: 50px;
    height: 55px;
    object-fit: cover;
}
    </style>
</head>

<body>

    <header>
        <div class="logo-container">
            <img src="{{ public_path('img/logoOAR.png') }}" alt="Logo" height="20px">
        </div>
        <div class="header-info">
            <h1> <strong> {{ $institucion }} </strong><br></h1>
            <p>Reporte general de impresiones</p>
            <h1> <strong> {{ $curso . '-' . $seccion }} </strong><br></h1>
            {{-- <p>Fecha de emisión: {{ date('d/m/Y H:i') }}</p> --}}
        </div>
    </header>

    <div class="seccion-detalles">
        <strong>CURSO:</strong> {{ $curso }} <br>
        <strong>SECCIÓN:</strong> {{ $seccion }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 12%;">DNI</th>
                <th style="width: 48%;">APELLIDOS Y NOMBRES</th>
                <th style="width: 15%;">MODALIDAD</th>
                <th style="width: 10%;">OBSERVACIÓN</th>
                <th tyle="width: 10%;">foto</th>
            </tr>

        </thead>
        <tbody>
            @foreach ($registros as $i => $r)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td style="text-align: center;">{{ $r->dni }}</td>
                    <td> {{ strtoupper($r->nombres) }} {{ strtoupper($r->apellidos) }}</td>
                    <td style="text-align: center;">{{ $r->modalidad }}</td>
                    <td style="text-align: center;">
                        @if ($r->observacion == 'Pendiente')
                            <span class="texto-rojo">{{ $r->observacion }}</span>
                        @else
                            {{ $r->observacion }}
                        @endif
                    </td>
                    <td>
                        @php
                            $rutaImagen = public_path($r->imagen);
                        @endphp

                        @if ($r->imagen && file_exists($rutaImagen))
                            <img src="{{ $rutaImagen }}" class="foto-perfil">
                        @else
                            <img src="{{ public_path('img/default.png') }}" class="foto-perfil">
                        @endif
                    </td>
            @endforeach
        </tbody>
    </table>
    @php
        $total = $registros->count();
        $pendientes = $registros->where('observacion', 'Pendiente')->count();
        $impresos = $total - $pendientes;
    @endphp
    <div class="footer-summary clearfix">
        <div class="contador-box">
            <span class="texto-rojo">PENDIENTES: {{ $pendientes }}</span><br>
            IMPRESOS: {{ $impresos }}<br>
            <span class="texto-total">TOTAL: {{ $total }}</span>
        </div>
    </div>

</body>

</html>
