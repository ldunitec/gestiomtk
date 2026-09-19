<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Liquidación #{{ $liquidacion->id }} - MEGATK</title>
    <style>
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 11px; 
            color: #2d3748; 
            line-height: 1.4; 
        }
        
        /* Encabezado */
        .company-header {
            width: 100%;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-header td {
            border: none;
            vertical-align: middle;
        }
        .logo {
            max-height: 55px;
            max-width: 180px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a365d;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            color: #4a5568;
            margin: 3px 0 0 0;
        }
        
        /* Tablas */
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 6px; 
            margin-bottom: 12px; 
        }
        .table th, .table td { 
            border: 1px solid #cbd5e0; 
            padding: 5px 8px; 
        }
        .table th { 
            background-color: #edf2f7; 
            font-size: 9.5px; 
            text-transform: uppercase; 
            color: #4a5568; 
        }
        .table-category-header {
            background-color: #ebf8ff;
            color: #2b6cb0;
            font-weight: bold;
            font-size: 11px;
            border-left: 4px solid #3182ce !important;
        }
        .table-subtotal {
            background-color: #f7fafc;
            font-weight: bold;
            font-size: 10.5px;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Badges de balance */
        .badge-balance {
            display: inline-block;
            padding: 5px 12px;
            font-weight: bold;
            font-size: 11px;
            border-radius: 4px;
        }
        .balance-devolucion { background-color: #feebc8; color: #c05621; border: 1px solid #fbd38d; }
        .balance-reembolso { background-color: #fed7d7; color: #9b2c2c; border: 1px solid #feb2b2; }
        .balance-saldado { background-color: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
        
        .summary-card { width: 48%; float: right; margin-top: 5px; }
        .clearfix::after { content: ""; clear: both; display: table; }
        
        /* Firmas */
        .signatures { margin-top: 45px; width: 100%; }
        .signatures td { border: none; text-align: center; width: 50%; }
        .signature-line { border-top: 1px solid #718096; width: 70%; margin: 0 auto 6px auto; }
    </style>
</head>
<body>

    {{-- Encabezado Empresarial --}}
    <table class="company-header">
        <tr>
            <td style="width: 25%;">
                {{-- Coloca tu logo en public/images/logo.png o ajusta la ruta --}}
                @if(file_exists(public_path('img/logo_mega.png')))
                    <img src="{{ public_path('img/logo_mega.png') }}" alt="MEGATK" class="logo">
                @else
                    <h1 style="margin:0; color:#1a365d; font-size:24px;">MEGATK</h1>
                @endif
            </td>
            <td style="width: 45%; text-align: center;">
                <div class="company-title" >MEGATK S.A.</div>
                <div class="doc-title">LIQUIDACIÓN DE GASTOS Y VIÁTICOS</div>
                <span style="color: #718096; font-size: 10px;">Liquidación #{{ str_pad($liquidacion->id, 5, '0', STR_PAD_LEFT) }}</span>
            </td>
            <td style="width: 30%; text-align: right; font-size: 10.5px;">
                <strong>Fecha Entrega:</strong> {{ $liquidacion->fecha_entrega ?? date('Y-m-d') }}<br>
                <strong>Destino/Ciudad:</strong> {{ $liquidacion->ciudad }}<br>
                <strong>Estado:</strong> {{ $liquidacion->estado == 1 ? 'Sin Entregar' : 'Entregado' }}
            </td>
        </tr>
    </table>

    {{-- Detalle de Gastos Agrupados por Tipo --}}
    @php
        $gastosAgrupados = $liquidacion->gastos->groupBy('tipo');
    @endphp

    <table class="table">
        <thead>
            <tr>
                <th style="width: 20%;">Fecha</th>
                <th style="width: 50%;">Ciudad / Detalle</th>
                <th class="text-right" style="width: 30%;">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gastosAgrupados as $tipo => $gastos)
                {{-- Encabezado de la Categoría --}}
                <tr>
                    <td colspan="3" class="table-category-header">
                         {{ strtoupper($tipo ?: 'GENERAL / OTROS') }}
                    </td>
                </tr>

                {{-- Ítems de la Categoría --}}
                @foreach($gastos as $gasto)
                    <tr>
                        <td class="text-center">{{ $gasto->fecha }}</td>
                        <td>{{ $gasto->ciudad }}</td>
                        <td class="text-right">L. {{ number_format($gasto->monto, 2) }}</td>
                    </tr>
                @endforeach

                {{-- Subtotal por Categoría --}}
                <tr class="table-subtotal">
                    <td colspan="2" class="text-right">Subtotal {{ $tipo }}:</td>
                    <td class="text-right">L. {{ number_format($gastos->sum('monto'), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="color: #a0aec0; padding: 15px;">
                        No existen gastos registrados para esta liquidación.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Resumen Financiero y Balances --}}
    <div class="clearfix">
        <table class="table summary-card">
            <tr>
                <td><strong>Monto Asignado (Anticipo):</strong></td>
                <td class="text-right">L. {{ number_format($montoAsignado, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Gastos Justificados:</strong></td>
                <td class="text-right">L. {{ number_format($totalGastos, 2) }}</td>
            </tr>
            <tr style="background-color: #edf2f7;">
                <td><strong>Diferencia / Balance:</strong></td>
                <td class="text-right"><strong>L. {{ number_format($montoBalance, 2) }}</strong></td>
            </tr>
            <tr>
                <td colspan="2" class="text-center" style="padding: 8px;">
                    @if($tipoBalance === 'DEVOLUCION')
                        <span class="badge-balance balance-devolucion">
                            MONTO A FAVOR DE MEGATK (DEVOLUCIÓN)
                        </span>
                    @elseif($tipoBalance === 'REEMBOLSO')
                        <span class="badge-balance balance-reembolso">
                            MONTO A FAVOR DEL COLABORADOR (REEMBOLSO)
                        </span>
                    @else
                        <span class="badge-balance balance-saldado">
                            CUENTA SALDADA EXACTA
                        </span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Bloque de Firmas --}}
    <table class="signatures">
        <tr>
            <td>
                <div class="signature-line"></div>
                <strong>Liquidado por</strong><br>
                <span>Firma del Empleado / Solicitante</span>
            </td>
            <td>
                <div class="signature-line"></div>
                <strong>Revisado y Aprobado</strong><br>
                <span>Administración / Contabilidad MEGATK</span>
            </td>
        </tr>
    </table>

</body>
</html>