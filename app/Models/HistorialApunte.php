<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialApunte extends Model
{
    protected $fillable = [
    'apunte_id',
    'password_anterior',
    'password_nueva',
    'cambiado_por',
    'motivo'
];

public function apunte()
{
    return $this->belongsTo(Apunte::class);
}
public function cambiadoPor()
{
    return $this->belongsTo(User::class, 'cambiado_por');
}


}
