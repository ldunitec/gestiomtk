<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlantillaCampo;
use App\Models\PlantillaCarnet;

class PlantillaCarnetSeeder extends Seeder
{
    public function run(): void
    {       // Crear plantilla CE-80 para empleados
        $plantilla = PlantillaCarnet::create([
            'nombre'=>'CR-80 Empleado',
            'aplica_a'=>'empleado',
            'fondo_front'=>['color'=>'#ffffff','image'=>'/images/fondo_front.png'],
            'fondo_back'=>['color'=>'#ffffff','image'=>'/images/fondo_back.png']
        ]);

 
        // Campos front
        PlantillaCampo::create([
            'plantilla_id'=>$plantilla->id,
            'lado'=>'front',
            'campo'=>'foto',
            'estilos'=>['x'=>20,'y'=>20,'w'=>120,'h'=>120,'font'=>0]
        ]);
        PlantillaCampo::create([
            'plantilla_id'=>$plantilla->id,
            'lado'=>'front',
            'campo'=>'nombre',
            'estilos'=>['x'=>160,'y'=>30,'w'=>200,'h'=>30,'font'=>18]
        ]);
        PlantillaCampo::create([
            'plantilla_id'=>$plantilla->id,
            'lado'=>'front',
            'campo'=>'cargo',
            'estilos'=>['x'=>160,'y'=>70,'w'=>200,'h'=>30,'font'=>14]
        ]);
        PlantillaCampo::create([
            'plantilla_id'=>$plantilla->id,
            'lado'=>'front',
            'campo'=>'departamento',
            'estilos'=>['x'=>160,'y'=>110,'w'=>200,'h'=>20,'font'=>12]
        ]);
        PlantillaCampo::create([
            'plantilla_id'=>$plantilla->id,
            'lado'=>'front',
            'campo'=>'qr',
            'estilos'=>['x'=>380,'y'=>20,'w'=>100,'h'=>100,'font'=>0]
        ]);

        // Campos back
        PlantillaCampo::create([
            'plantilla_id'=>$plantilla->id,
            'lado'=>'back',
            'campo'=>'informacion_contacto',
            'estilos'=>['x'=>20,'y'=>20,'w'=>400,'h'=>100,'font'=>12]
        ]);
        PlantillaCampo::create([
            'plantilla_id'=>$plantilla->id,
            'lado'=>'back',
            'campo'=>'codigo',
            'estilos'=>['x'=>20,'y'=>130,'w'=>200,'h'=>30,'font'=>14]
        ]);
    }
}
