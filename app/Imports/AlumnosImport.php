<?php

namespace App\Imports;

use App\Models\Alumno;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class AlumnosImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        /*
        ==========================================
        DATOS GENERALES (fila índice 8)
        ==========================================
        */


        $titulo = $rows[1][1] ?? null;
        // dd($titulo);
        if ($titulo === 'Jardin, Escuela y Liceo Bilingue Cardenal') {


            // =========================
            // DATOS GENERALES
            // =========================

            $curso = $rows[9][5] ?? null;
            $seccion = $rows[9][15] ?? null;
            $jornada = $rows[7][11] ?? null;
            $modalidadOriginal = $rows[7][17] ?? null; // ajusta si cambia
            // dd($modalidad);

 if ($modalidadOriginal) {
                $modalidad = mb_strtoupper(trim($modalidadOriginal), 'UTF-8');
            }
            // =========================
            // RECORRER TODAS LAS FILAS
            // =========================

            foreach ($rows as $row) {

                // Si no tiene DNI válido, saltar
                if (!isset($row[10]) || !is_numeric($row[10])) {
                    continue;
                }

                $dni = $this->formatearDNI($row[10]);

                $nombreSeparado = $this->separarNombre($row[13] ?? '');

                Alumno::updateOrCreate(
                    ['dni' => $dni],
                    [
                        'curso' => $curso,
                        'jornada' => $jornada,
                        'seccion' => $seccion,
                        'modalidad' => $modalidad,
                        'nombres' => $nombreSeparado['nombres'],
                        'apellidos' => $nombreSeparado['apellidos'],
                        'observacion' => $row[25] ?? null,
                    ]
                );
            }


        } else {
            # code.
            $seccion = $rows[8][15] ?? null;   // 2
            $jornada = $rows[6][9] ?? null;   // I Semestre
            $curso = $rows[8][5] ?? null;   // I Semestre
            // $modalidad = $rows[6][16] ?? null;   // Duodécimo
            $modalidadOriginal = $rows[6][16] ?? null;
            if ($modalidadOriginal) {
                $modalidad = mb_strtoupper(trim($modalidadOriginal), 'UTF-8');
            }
            $modalidad = $this->abreviarModalidad($modalidadOriginal);

            // dd($curso);
            /*9
            ==========================================
            ALUMNOS (desde índice 16 en adelante)
            ==========================================
            */

            foreach ($rows->slice(16) as $row) {

                if (!isset($row[7]))
                    continue; // DNI obligatorio

                $dni = $this->formatearDNI($row[7]);

                $nombreSeparado = $this->separarNombre($row[13] ?? '');

                Alumno::updateOrCreate(
                    ['dni' => $dni],
                    [
                        'curso' => $curso,
                        'jornada' => $jornada,
                        'seccion' => $seccion,
                        'modalidad' => $modalidad,
                        'nombres' =>  $nombreSeparado['nombres'],
                        'apellidos' => $nombreSeparado['apellidos'],
                        'observacion' => $row[25] ?? null,
                    ]
                );
            }
        }
    }

    /*
    ==========================================
    FORMATEAR DNI
    Ejemplo:
    313200800067
    → 00313-2008-00067
    ==========================================
    */

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

    // private function separarNombre($nombreCompleto)
    // {
    //     $partes = preg_split('/\s+/', trim($nombreCompleto));

    //     return [
    //         'nombres' => implode(' ', array_slice($partes, 0, 2)),
    //         'apellidos' => implode(' ', array_slice($partes, 2))
    //     ];
    // }

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





    //     private function abreviarModalidad($modalidad)
// {
//     $modalidad = trim($modalidad);

    //     if (stripos($modalidad, 'Bachillerato Técnico Profesional') !== false) {
//         return 'BTP';
//     }

    //     if (stripos($modalidad, 'Bachillerato en Ciencias y Humanidades') !== false) {
//         return 'BCH';
//     }

    //     if (stripos($modalidad, 'Comercio') !== false) {
//         return 'BC';
//     }

    //     return $modalidad; // si no coincide, guarda original
// }

    private function abreviarModalidad($modalidad)
    {
        $map = [
            'Bachillerato Técnico Profesional En Informática ISEMED' => 'BTP Informática ISEMED',
            'Bachillerato en Ciencias y Humanidades Acelerado ISEMED' => 'BCH ACELERADO ISEMED',
            'III Ciclo De Educación Básica ISEMED' => 'III-CEB ISEMED',
            'Bachillerato En Ciencias Y Humanidades ISEMED' => 'BCH ISEMED',
            'Bachillerato en Informática' => 'BTI'
        ];

        foreach ($map as $texto => $abreviatura) {
            if (stripos($modalidad, $texto) !== false) {
                return $abreviatura;
            }
        }

        return $modalidad;
    }
}