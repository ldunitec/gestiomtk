<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Reporte de Equipos</title>

    <style>
        @page {
            margin: 35px 30px 40px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #1f4e78;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
        }

        .titulo {
            font-size: 18px;
            font-weight: bold;
            color: #1f4e78;
        }

        .subtitulo {
            font-size: 10px;
            color: #666;
            margin-top: 4px;
        }

        .fecha {
            text-align: right;
            font-size: 9px;
            color: #555;
        }

        .seccion {
            margin-top: 12px;
            margin-bottom: 6px;
            padding: 7px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        .activo {
            background-color: #198754;
        }

        .inactivo {
            background-color: #dc3545;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: #e9ecef;
            border: 1px solid #bbb;
            padding: 5px;
            text-align: center;
            font-weight: bold;
        }

        td {
            border: 1px solid #ccc;
            padding: 5px;
            vertical-align: middle;
            font-size: 12px;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .codigo {
            font-family: DejaVu Sans Mono, monospace;
        }

        .estado-activo {
            color: #198754;
            font-weight: bold;
        }

        .estado-inactivo {
            color: #dc3545;
            font-weight: bold;
        }

        .resumen {
            width: 100%;
            margin-bottom: 15px;
        }

        .resumen td {
            border: none;
            padding: 3px;
        }

        .total-activos {
            color: #198754;
            font-weight: bold;
        }

        .total-inactivos {
            color: #dc3545;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #777;
        }

        .page-break {
            page-break-before: always;
        }

        .tipo-titulo {
            background-color: #f1f3f5;
            border-left: 4px solid #1f4e78;
            padding: 6px 8px;
            margin-top: 10px;
            margin-bottom: 4px;
            font-size: 10px;
            font-weight: bold;
            color: #1f4e78;
        }

        .tipo-titulo span {
            font-size: 8px;
            color: #666;
            font-weight: normal;
        }
    </style>

</head>

<body>

    {{-- ENCABEZADO --}}

    <div class="header">

        <table class="header-table">

            <tr>

                <td style="border:none; width:70%;">

                    <div class="titulo">
                        GESTIÓN MTK
                    </div>

                    <div class="subtitulo">
                        Reporte de Inventario de Equipos
                    </div>

                </td>

                <td style="border:none; width:30%;" class="fecha">

                    Fecha de impresión:<br>

                    <strong>
                        {{ $fecha }}
                    </strong>

                </td>

            </tr>

        </table>

    </div>


    {{-- RESUMEN --}}

    <table class="resumen">

        <tr>

            <td>
                Total de equipos activos:
                <span class="total-activos">
                    {{ $activos->count() }}
                </span>
            </td>

            <td>
                Total de equipos inactivos:
                <span class="total-inactivos">
                    {{ $inActivos->count() }}
                </span>
            </td>

            <td>
                Total general:
                <strong>
                    {{ $activos->count() + $inActivos->count() }}
                </strong>
            </td>

        </tr>

    </table>


    {{-- EQUIPOS ACTIVOS --}}
    {{-- EQUIPOS ACTIVOS --}}

    <div class="seccion activo">
        EQUIPOS ACTIVOS
    </div>

    @foreach ($activosPorTipo as $tipo => $equipos)
        <div class="tipo-titulo">
            {{ strtoupper($tipo) }}
            <span>
                ({{ $equipos->count() }} equipo{{ $equipos->count() != 1 ? 's' : '' }})
            </span>
        </div>

        <table>

            <thead>
                <tr>

                    <th style="width:35px;">
                        N°
                    </th>

                    <th>
                        Asignado
                    </th>

                    <th>
                        IP Address
                    </th>

                    <th>
                        MAC Address
                    </th>

                    <th>
                        Nombre PC
                    </th>

                    <th>
                        Antivirus
                    </th>

                    <th>
                        Estado
                    </th>

                </tr>
            </thead>

            <tbody>

                @foreach ($equipos as $equipo)
                    <tr>

                        <td class="center">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $equipo->empleado ? $equipo->empleado->nombres . ' ' . $equipo->empleado->apellidos : 'Sin asignación' }}
                        </td>

                        <td class="codigo">
                            {{ $equipo->ip_address ?? 'N/A' }}
                        </td>

                        <td class="codigo">
                            {{ $equipo->mac_address ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $equipo->nombre ?? 'N/A' }}
                        </td>

                        <td class="center">
                            {{ $equipo->antivirus ?? 'N/A' }}
                        </td>

                        <td class="center estado-activo">
                            {{ $equipo->estado }}
                        </td>

                    </tr>
                @endforeach

            </tbody>

        </table>
    @endforeach

    {{-- EQUIPOS INACTIVOS --}}
   <div class="page-break"></div>

<div class="seccion inactivo">
    EQUIPOS INACTIVOS
</div>

@foreach($inActivosPorTipo as $tipo => $equipos)

    <div class="tipo-titulo">
        {{ strtoupper($tipo) }}
        <span>
            ({{ $equipos->count() }} equipo{{ $equipos->count() != 1 ? 's' : '' }})
        </span>
    </div>

    <table>

        <thead>
            <tr>

                <th style="width:35px;">
                    N°
                </th>

                <th>
                    Asignado
                </th>

                <th>
                    IP Address
                </th>

                <th>
                    MAC Address
                </th>

                <th>
                    Nombre PC
                </th>

                <th>
                    Estado
                </th>

            </tr>
        </thead>

        <tbody>

            @foreach($equipos as $equipo)

                <tr>

                    <td class="center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $equipo->empleado
                            ? $equipo->empleado->nombres . ' ' . $equipo->empleado->apellidos
                            : 'Sin asignación' }}
                    </td>

                    <td class="codigo">
                        {{ $equipo->ip_address ?? 'N/A' }}
                    </td>

                    <td class="codigo">
                        {{ $equipo->mac_address ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $equipo->nombre ?? 'N/A' }}
                    </td>

                    <td class="center estado-inactivo">
                        {{ $equipo->estado }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

@endforeach

</body>

</html>
