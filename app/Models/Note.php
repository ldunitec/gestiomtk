<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// app/Models/Note.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Note.php
class Note extends Model
{
    protected $fillable = [
        'brand_id',
        'titulo',
        'nota'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}


