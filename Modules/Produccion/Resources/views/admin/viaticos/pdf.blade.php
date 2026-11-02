
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Viático #{{ $viatico->id }}</title>

    <style>
        @page {
            margin: 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            border-bottom: 2px solid #1f4e79;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #1f4e79;
            font-size: 21px;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #666;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        th, td {
            border: 1px solid #d6dce4;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #edf2f7;
        }

        .section {
            background-color: #1f4e79;
            color: #fff;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .total {
            background-color: #e8f3e8;
            font-size: 15px;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 35px;
            text-align: center;
            font-size: 9px;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>MEGATK</h1>
        <strong>REPORTE DE VIÁTICOS</strong>
        <div class="subtitle">
            Documento de liquidación de gastos de viaje
        </div>
    </div>

    <table>
        <tr>
            <th>Viático N.º</th>
            <td>{{ $viatico->id }}</td>
            <th>Fecha de emisión</th>
            <td>{{ now()->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Origen</th>
            <td colspan="3">{{ $viatico->origen ?? 'No registrado (viático anterior)' }}</td>
        </tr>
        <tr>
            <th>Destino</th>
            <td colspan="3">{{ $viatico->ciudad }}</td>
        </tr>
    </table>

    <div class="section">Información del viaje</div>

    <table>
        <tr>
            <th>Distancia de ida</th>
            <td>{{ number_format($viatico->distancia_km, 2) }} km</td>
            <th>Días</th>
            <td>{{ $viatico->dias }}</td>
        </tr>
        <tr>
            <th>Noches de hospedaje</th>
            <td>{{ $viatico->noches }}</td>
            <th>Cantidad de técnicos</th>
            <td>{{ $viatico->cantidad_tecnicos }}</td>
        </tr>
        <tr>
            <th>Peajes (L)</th>
            <td>{{ $viatico->peajes }}</td>
            <th> </th>
            <td> </td>
        </tr>
    </table>

    <div class="section">Detalle de gastos</div>

    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Importe (L)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Combustible</td>
                <td class="text-right">
                    L {{ number_format($viatico->combustible, 2) }}
                </td>
            </tr>
            <tr>
                <td>Alimentación</td>
                <td class="text-right">
                    L {{ number_format($viatico->alimentacion, 2) }}
                </td>
            </tr>
            <tr>
                <td>Peajes</td>
                <td class="text-right">
                    L {{ number_format($viatico->peajes, 2) }}
                </td>
            </tr>
            <tr>
                <td>Hospedaje</td>
                <td class="text-right">
                    L {{ number_format($viatico->hospedaje, 2) }}
                </td>
            </tr>
            <tr>
                <td>Gastos extras</td>
                <td class="text-right">
                    L {{ number_format($viatico->extra, 2) }}
                </td>
            </tr>
            <tr>
                <td>Mano de obra (monto total)</td>
                <td class="text-right">L {{ number_format($viatico->mano_obra ?? 0, 2) }}</td>
            </tr>
            <tr class="total">
                <td>TOTAL GENERAL (VIÁTICOS + MANO DE OBRA)</td>
                <td class="text-right">
                    L {{ number_format($viatico->total, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <table>
        <tr>
            <td style="height: 45px; text-align: center;">
                ___________________________<br>
                Elaborado por
            </td>
            <td style="height: 45px; text-align: center;">
                ___________________________<br>
                Autorizado por
            </td>
        </tr>
    </table>

    <div class="footer">
        MEGATK | Reporte generado por el sistema de control de viáticos.
    </div>

</body>
</html>

