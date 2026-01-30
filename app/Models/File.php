<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/File.php
class File extends Model
{
    protected $fillable = [
        'brand_id',
        'model',
        'type',
        'name',
        'link_drive'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}

