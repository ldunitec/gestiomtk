<?php
namespace Modules\NetLan\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function empleado()
    {
        return $this->hasMany(Empleado::class);
    }
}
