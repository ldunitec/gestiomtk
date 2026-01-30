<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
       protected $fillable = [
        'model_id',
        'type',
        'file_path',
        'drive_path'
    ];

    public function modelDevice()
    {
        return $this->belongsTo(Modelo::class, 'model_id');
    }
    use HasFactory;
}
