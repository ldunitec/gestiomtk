<?php

namespace App\Imports;

use App\Models\Crey;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CreyImport implements ToCollection
{

    public function collection(Collection $rows)
{
    $config = [
        'nombre_index' => 1,
        'dni_index' => 2,
        'grado_index' => 3,
        'seccion_index' => 4,
        'modalidad_index' => 5,
        'observaciones_index' => 6,
        'slice' => 1 // saltar encabezado
    ];

    $dataRows = $rows->slice($config['slice']);

    // dd($dataRows);

    foreach ($dataRows as $row) {

        // Si el DNI está vacío, no guardar
        if (empty($row[$config['dni_index']])) {
            continue;
        }

        $nombreSeparado = $this->separarNombre($row[$config['nombre_index']] ?? '');

        Crey::updateOrCreate(
            ['dni' => $row[$config['dni_index']]],
            [
                'curso' => $row[$config['grado_index']] ?? null,
                'seccion' => $row[$config['seccion_index']] ?? null,
                'modalidad' => $row[$config['modalidad_index']] ?? null,
                'nombres' => $nombreSeparado['nombres'],
                'apellidos' => $nombreSeparado['apellidos'],
                'observaciones' => $row[$config['observaciones_index']] ?? 'Pendiente',
            ]
        );
    }
}

    private function separarNombre($nombreCompleto)
    {

        $nombreCompleto = trim(mb_strtoupper($nombreCompleto));

        $partes = preg_split('/\s+/', $nombreCompleto);

        if (count($partes) < 3) {
            return [
                'nombres' => $nombreCompleto,
                'apellidos' => ''
            ];
        }

        $apellidos = implode(' ', array_slice($partes, -2));
        $nombres = implode(' ', array_slice($partes, 0, -2));

        return [
            'nombres' => ucwords(mb_strtolower($nombres)),
            'apellidos' => ucwords(mb_strtolower($apellidos))
        ];
    }

}