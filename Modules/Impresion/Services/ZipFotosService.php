<?php

namespace Modules\Impresion\Services;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class ZipFotosService
{
    public function __construct(protected FotoService $fotos)
    {
    }

    public function build(Collection $alumnos): array
    {
        if (!class_exists(ZipArchive::class)) {
            throw ValidationException::withMessages(['lote' => 'Habilita la extensión ZIP de PHP para generar archivos.']);
        }
        $dir = storage_path('app/impresion/temporales');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $path = tempnam($dir, 'fotos_');
        $zip = new ZipArchive();
        $csv = fopen('php://temp', 'r+');
        $abierto = false;
        try {
            if (!$path || $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('No se pudo crear el ZIP.');
            }
            $abierto = true;
            $incluidos = [];
            $faltantes = [];
            $usados = [];
            $bytes = 0;
            fputcsv($csv, ['CURSO', 'SECCION', 'MODALIDAD', 'NOMBRE', 'DNI', 'RESULTADO', 'ARCHIVO']);
            foreach ($alumnos as $alumno) {
                $nombre = $this->fotos->safeName($alumno->nombres.' '.$alumno->apellidos);
                $foto = $this->fotos->resolve($alumno);
                $archivo = '';
                if ($foto) {
                    $bytes += filesize($foto['path']);
                    if ($bytes > config('impresion.max_zip_bytes', 104857600)) {
                        throw ValidationException::withMessages(['lote' => 'El lote supera 100 MB. Divide la sección en selecciones más pequeñas.']);
                    }
                    $folder = $this->fotos->safeName($alumno->curso).'/'
                        .$this->fotos->safeName($alumno->seccion).'/'
                        .$this->fotos->safeName($alumno->modalidad, 'SIN MODALIDAD').'/';
                    $archivo = $folder.$nombre.'.'.$foto['extension'];
                    // Evitar sobrescribir fotos de personas con el mismo nombre.
                    $colision = 0;
                    while (isset($usados[mb_strtolower($archivo)])) {
                        $colision++;
                        $archivo = $folder.$nombre.' - ID '.$alumno->id
                            .($colision > 1 ? ' '.$colision : '').'.'.$foto['extension'];
                    }
                    $usados[mb_strtolower($archivo)] = true;
                    if (!$zip->addFile($foto['path'], $archivo)) {
                        throw new \RuntimeException('No se pudo agregar una foto al ZIP.');
                    }
                    $incluidos[] = $alumno->id;
                } else {
                    $faltantes[] = $alumno->id;
                }
                $cells = [$alumno->curso, $alumno->seccion, $alumno->modalidad, $nombre,
                    $alumno->dni, $foto ? 'INCLUIDA' : 'SIN FOTO', $archivo];
                fputcsv($csv, array_map(fn ($v) => preg_match('/^[=+\-@\t\r]/', (string) $v) ? "'".$v : $v, $cells));
            }
            if (!$incluidos) {
                throw ValidationException::withMessages(['lote' => 'Ningún registro del lote tiene una foto disponible. No se marcó ninguna impresión.']);
            }
            rewind($csv);
            if (!$zip->addFromString('RESUMEN.csv', "\xEF\xBB\xBF".stream_get_contents($csv)) || !$zip->close()) {
                throw new \RuntimeException('No se pudo finalizar el ZIP.');
            }
            $abierto = false;

            return compact('path', 'incluidos', 'faltantes');
        } catch (\Throwable $e) {
            if ($abierto) {
                $zip->close();
            }
            if ($path && is_file($path)) {
                unlink($path);
            }
            throw $e;
        } finally {
            fclose($csv);
        }
    }
}
