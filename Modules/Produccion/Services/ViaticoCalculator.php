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

        return $gastos + ['total' => round(array_sum($gastos), 2)];
    }
}
