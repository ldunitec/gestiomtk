<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwitchMIK extends Model
{
    use HasFactory;
      protected $fillable = [
        'name','location','total_ports',
    ];
    public function ports()
    {
        return $this->hasMany(Port::class);
    }
    public function swports()
    {
        return $this->hasMany(SwPort::class,);
    }
}
