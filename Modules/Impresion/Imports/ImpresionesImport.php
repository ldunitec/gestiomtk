<?php

namespace Modules\Impresion\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Impresion\Entities\Crey;
use Modules\Impresion\Services\FotoService;

class ImpresionesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $dni = trim((string) ($row['dni'] ?? ''));
        if ($dni === '') {
            return null;
        }
        $nombre = preg_split('/\s+/u', trim((string) ($row['nombre'] ?? '')), -1, PREG_SPLIT_NO_EMPTY);
        $nombres = trim((string) ($row['nombres'] ?? implode(' ', array_slice($nombre, 0, count($nombre) > 2 ? -2 : 1))));
        $apellidos = trim((string) ($row['apellidos'] ?? implode(' ', array_slice($nombre, count($nombre) > 2 ? -2 : 1))));
        if ($nombres === '') {
            throw new \InvalidArgumentException('Una fila con DNI no tiene nombre.');
        }
        $alumno = Crey::firstOrNew(['dni' => $dni]);
        $codigoFoto = trim((string) ($row['foto'] ?? ''));
        $alumno->fill([
            'curso' => $row['curso'] ?? null,
            'modalidad' => $row['modalidad'] ?? null,
            'seccion' => $row['seccion'] ?? null,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'observaciones' => $codigoFoto ?: 'Pendiente',
        ]);
        // La carpeta original es opcional y se configura por servidor, nunca se usa H: fijo.
        // Si no hay foto física, se importa el alumno y el panel mostrará "Sin foto".
        if (!$alumno->foto_path && $codigoFoto !== ''
            && ($foto = app(FotoService::class)->importOriginal($codigoFoto))) {
            $alumno->foto_path = $foto;
        }
        if ($alumno->isDirty(['curso', 'modalidad', 'seccion', 'nombres', 'apellidos', 'observaciones', 'foto_path'])) {
            $alumno->impreso_at = null;
            $alumno->impreso_por = null;
        }

        return $alumno;
    }
}
