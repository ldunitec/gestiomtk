<?php

namespace Modules\Impresion\Imports;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Impresion\Entities\Crey;

class ImpresionesImport implements ToModel, WithHeadingRow
{
public function model(array $row)
{
    if (empty($row['dni'])) {
        return null;
    }

    $nombreSeparado = $this->separarNombre($row['nombre'] ?? '');
    $foto = $row['foto'] ?? null;

    if ($foto) {
        // Aseguramos que tenga extensión para buscar el archivo
        $nombreArchivoBusqueda = Str::endsWith($foto, '.jpg') ? $foto : $foto . '.jpg';

        $this->procesarImagen(
            $nombreArchivoBusqueda,
            $nombreSeparado['nombres'],
            $nombreSeparado['apellidos'],
            $row['curso'] ?? 'sin_curso',
            $row['seccion'] ?? 'sin_seccion',
            $row['modalidad'] ?? 'sin_modalidad'
        );
    }

    return new Crey([
        'curso' => $row['curso'] ?? null,
        'modalidad' => $row['modalidad'] ?? null,
        'seccion' => $row['seccion'] ?? null,
        'dni' => $row['dni'],
        'nombres' => $nombreSeparado['nombres'],
        'apellidos' => $nombreSeparado['apellidos'],
        'observaciones' => $row['foto'] ?? 'Pendiente',
    ]);
}
private function procesarImagen($nombreOriginal, $nombres, $apellidos, $curso, $seccion, $modalidad)
{
    try {

        $rutaOrigen = "H:/Cristo rey/crey/" . trim($nombreOriginal);

        $cursoFolder = Str::upper(Str::slug($curso, '_'));
        $modalidadFolder = Str::upper(Str::slug($modalidad, '_'));
        $seccionFolder = Str::upper(Str::slug($seccion, '_'));

        $pathDestinoBase = public_path(
            "uploads/Crey/Estudiantes/{$cursoFolder}/{$modalidadFolder}/{$seccionFolder}"
        );

        // Nombre limpio
        $nuevoNombre = Str::upper(
            Str::slug($nombres . ' ' . $apellidos, '_')
        ) . '.jpg';

        $rutaCompletaDestino = $pathDestinoBase . '/' . $nuevoNombre;

        // DEBUG
        if (!File::exists($rutaOrigen)) {

            dd([
                'error' => 'NO EXISTE ARCHIVO ORIGEN',
                'ruta' => $rutaOrigen
            ]);
        }

        // Crear carpeta
        if (!File::isDirectory($pathDestinoBase)) {
            File::makeDirectory($pathDestinoBase, 0775, true, true);
        }

        // Copiar
        $copiado = File::copy($rutaOrigen, $rutaCompletaDestino);

        if (!$copiado) {
            dd([
                'error' => 'NO SE PUDO COPIAR',
                'origen' => $rutaOrigen,
                'destino' => $rutaCompletaDestino
            ]);
        }

        return $rutaCompletaDestino;

    } catch (\Exception $e) {

        dd([
            'mensaje' => $e->getMessage(),
            'linea' => $e->getLine()
        ]);
    }
}
      private function separarNombre($nombreSeparado)
    {

        $nombreSeparado = trim(mb_strtoupper($nombreSeparado));

        $partes = preg_split('/\s+/', $nombreSeparado);

        if (count($partes) < 3) {
            return [
                'nombres' => $nombreSeparado,
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