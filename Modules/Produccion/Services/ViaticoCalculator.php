<?php

namespace Modules\Produccion\Services;

class ViaticoCalculator
{
    public function calculate(array $data): array
    {
        // Se conserva la fórmula histórica: ida y vuelta, 30 km locales y 15% adicional.
        $combustible = round(ceil(($data['distancia_km'] * 2 + 30) / 40) * $data['precio_combustible'] * 1.15, 2);
        $gastos = [
            'combustible' => $combustible,
            'alimentacion' => ceil($data['cantidad_tecnicos'] * $data['dias'] * $data['precio_alimentacion']),
            'peajes' => ceil($data['precio_peaje'] * $data['peajes']),
            'hospedaje' => ceil($data['precio_hotel'] * $data['noches']),
            'extra' => ceil($data['extra']),
            'mano_obra' => round((float) $data['mano_obra'], 2),
        ];

        // El impuesto se aplica una sola vez al subtotal completo, incluida mano de obra.
        // Usar centavos evita errores de coma flotante al redondear medios centavos.
        $subtotalCentavos = (int) round(array_sum($gastos) * 100);
        $impuestoCentavos = intdiv($subtotalCentavos * 15 + 50, 100);

        return $gastos + [
            'subtotal' => $subtotalCentavos / 100,
            'impuesto_porcentaje' => 15,
            'impuesto' => $impuestoCentavos / 100,
            'total' => ($subtotalCentavos + $impuestoCentavos) / 100,
        ];
    }
}
