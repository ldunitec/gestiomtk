<?php

namespace App\Imports;

use App\Models\Alumno;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AlumnosImport implements ToCollection
{


// nuevo collect 
    ///////////////
    public function collection(Collection $rows)
{
    $titulo = $rows[1][1] ?? null;
    $curso  = $rows[9][5] ?? null;
    $dia  = $rows[0][0] ?? null;

                // dd($dia);


    switch ($curso) {

        case 'Preparatoria':
            $config = [
                'dni_index' => 9,
                'nombre_index' => 13,
                'seccion' => $rows[9][15] ?? null,
                'jornada' => $rows[7][11] ?? null,
                'modalidad' => $rows[7][17] ?? null,
                'slice' => 0
            ];
            break;

        case 'Tercero':
            $config = [
                'dni_index' => 9,
                'nombre_index' => 12,
                'seccion' => $rows[9][14] ?? null,
                'jornada' => $rows[7][10] ?? null,
                'modalidad' => $rows[7][16] ?? null,
                'slice' => 16
            ];
            break;
        case 'Duodécimo':
            $config = [
                'dni_index' => 9,
                'nombre_index' => 12,
                'seccion' => $rows[9][14] ?? null,
                'jornada' => $rows[7][10] ?? null,
                'modalidad' => $rows[7][16] ?? null,
                'slice' => 0
            ];
            break;

        default:
            $config = [
                'dni_index' => 10,
                'nombre_index' => 13,
                'seccion' => $rows[9][15] ?? null,
                'jornada' => $rows[7][10] ?? null,
                'modalidad' => $rows[7][17] ?? null,
                'slice' => 0
            ];
    }
// dd($config['modalidad']);
    $modalidad = $this->abreviarModalidad(
        $config['modalidad']
            ? mb_strtoupper(trim($config['modalidad']), 'UTF-8')
            : null
    );
   

    $dataRows = $config['slice'] > 0
        ? $rows->slice($config['slice'])
        : $rows;

    foreach ($dataRows as $row) {

        if (!isset($row[$config['dni_index']]) || !is_numeric($row[$config['dni_index']])) {
            continue;
        }

        $dni = $this->formatearDNI($row[$config['dni_index']]);
        $nombreSeparado = $this->separarNombre($row[$config['nombre_index']] ?? '');
$observacion = $row[29] ?? 'Pendiente';

$observacion = preg_replace("/[^A-Za-z0-9\s\-_]/", "_", $observacion);

// imagen>>>>>>>>>>>>>>>

$foto = $observacion ?? null;
       
        if ($foto && $foto !== 'Pendiente') {

            if (!Str::endsWith($foto, '.jpg')) {
                $foto .= '.jpg';
            }

            $this->procesarImagen(
                $titulo,
                $foto,
                $nombreSeparado['nombres'],
                $nombreSeparado['apellidos'] ?? '',
                $curso ?? '',
                $config['seccion'] ?? '',
                $modalidad ?? '',
                $dia ?? ''
            );
        }
$resultadoImagen = $this->procesarImagen(
    $titulo,
    $foto,
    $nombreSeparado['nombres'],
    $nombreSeparado['apellidos'] ?? '',
    $curso ?? '',
    $config['seccion'] ?? '',
    $modalidad ?? '',
    $dia ?? ''
);

$imagen = $resultadoImagen['imagen'] ?? null;

        Alumno::updateOrCreate(
            ['dni' => $dni],
            [
                'institucion'=>$titulo,
                'curso' => $curso,
                'jornada' => $config['jornada'],
                'seccion' => $config['seccion'],
                'modalidad' => $modalidad,
                'nombres' => $nombreSeparado['nombres'],
                'apellidos' => $nombreSeparado['apellidos'],
                'observacion' =>$observacion,
                'imagen' =>$imagen 
             
            ]
        );
    }
}


 private function procesarImagen($titulo,$nombreOriginal, $nombres, $apellidos, $curso, $seccion, $modalidad,$dia)
    {
        // $rutaOrigen = public_path('crey/' . $nombreOriginal);
      // Opción recomendada (Barras normales y asegurando el slash final)
$rutaOrigen = "H:/impresion final/LICEO CARDENAL1/" . $nombreOriginal;

        $cursoFolder = Str::slug($curso, '_');
        $seccionFolder = Str::slug($seccion, '_');
        $modalidadFolder = Str::slug($modalidad, '_');
        $tituloFolder = Str::slug($titulo, '_');
        $diaFolder = Str::slug($dia, '_');

if ($dia=='DOMINGO') {
    $pathDestinoBase = public_path("uploads/LCOAR/{$tituloFolder}/{$diaFolder}/estudiantes/{$cursoFolder}/$modalidadFolder/{$seccionFolder}");

} else {
     $pathDestinoBase = public_path("uploads/LCOAR/{$tituloFolder}/estudiantes/{$cursoFolder}/$modalidadFolder/{$seccionFolder}");

}



       
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);

        $nuevoNombre = Str::upper(
            Str::slug($nombres . ' ' . $apellidos, ' ')
        ) . '.' . $extension;

        $rutaCompletaDestino = $pathDestinoBase . '/' . $nuevoNombre;

        if (File::exists($rutaOrigen)) {

            if (!File::isDirectory($pathDestinoBase)) {
                File::makeDirectory($pathDestinoBase, 0777, true, true);
            }

            File::copy($rutaOrigen, $rutaCompletaDestino);

                  
        }
      return ['imagen'=> str_replace(public_path(), '', $rutaCompletaDestino)];
    }

    private function formatearDNI($dni)
    {
        $dni = preg_replace('/\D/', '', $dni);
        $dni = str_pad($dni, 13, '0', STR_PAD_LEFT);

        return substr($dni, 0, 4) . '-' .
            substr($dni, 4, 4) . '-' .
            substr($dni, 8, 5);
    }

    /*
    ==========================================
    SEPARAR NOMBRES Y APELLIDOS
    (maneja 2 nombres + 2 apellidos)
    ==========================================
    */

  

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



    private function abreviarModalidad($modalidad)
    {
        $map = [
            'Bachillerato Técnico Profesional En Informática ISEMED' => 'BTP INFORMATICA ISEMED',
            'Bachillerato en Ciencias y Humanidades Acelerado ISEMED' => 'BCH ACELERADO ISEMED',
            'III Ciclo De Educación Básica ISEMED' => 'III C.E.B. – ISEMED',
            'Bachillerato En Ciencias Y Humanidades ISEMED' => 'BCH ISEMED',
            'Bachillerato En Ciencias Y Humanidades Acelerado ISEMED' => 'BCH ACELERADO ISEMED',
            'Bachillerato en Informática' => 'BT IINFORMATICA',
            'SEGUNDO GRADO DE PREBÁSICA' => '2do. PREBÁSICA',
            'TERCER GRADO DE PREBÁSICA' => '3ro. PREBÁSICA',
            'PRIMER CICLO DE EDUCACIÓN BÁSICA' => 'I CICLO E.B.',
            'SEGUNDO CICLO DE EDUCACIÓN BÁSICA' => 'II CICLO E.B.',
            'III CICLO DE EDUCACIÓN BÁSICA' => 'III CICLO E.B.',
            'TERCER CICLO DE EDUCACIÓN BÁSICA' => 'III CICLO E.B.',
            'Bachillerato Tecnico Profesional Asistente Ejecutivo Bilingue' => 'B.T.P. ASIST. EJEC. BILINGUE',
            'Bachillerato en Ciencias y Humanidades' => 'B.C.H.',
            'BACHILLERATO TÉCNICO PROFESIONAL EN INFORMÁTICA'=>'B.T.P. INFORMATICA'
        ];
        foreach ($map as $texto => $abreviatura) {
            if (stripos($modalidad, $texto) !== false) {
                return $abreviatura;
            }
        }

        return $modalidad;
    }
}