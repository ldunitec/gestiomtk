<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    use HasFactory;
    protected $fillable = [
        'patch_panel_id','port_number',
    ];
    public function patch_panel()
    {
        return $this->belongsTo(PatchPanel::class);
    }
    public function switchMik()
    {
        return $this->belongsTo(switchMik::class);
    }
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
    public function connection()
    {
        return $this->hasOne(Connection::class);
    }
}



