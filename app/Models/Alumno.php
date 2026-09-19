<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Alumno extends Model
{
    protected $fillable = [
        'institucion',
        'curso',
        'jornada',
        'seccion',
        'modalidad',
        'dni',
        'nombres',
        'apellidos',
        'observacion',
        'imagen'
    ];




    // app/Models/Alumno.php



public function getFotoUrlAttribute()
{
    $cursoFolder = Str::slug($this->curso, '_');
    $seccionFolder = Str::slug($this->seccion, '_');
    $nombreArchivo = Str::upper(Str::slug($this->nombres . ' ' . $this->apellidos, ' ')) . '.jpg';

    $path = "uploads/LCOAR/estudiantes/{$cursoFolder}/{$seccionFolder}/{$nombreArchivo}";

    // Verificamos si el archivo existe físicamente en la carpeta public
    if (file_exists(public_path($path))) {
        return asset($path);
    }

    // Retornar una imagen por defecto si no existe
    return asset('images/default-avatar.png');
}
}


