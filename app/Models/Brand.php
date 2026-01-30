<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Brand.php
class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'color_primary'
    ];

     // Siempre guardar name en mayúsculas
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }

    // Siempre guardar slug en minúsculas
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strtolower($value);
    }

public function files()
{
    return $this->hasMany(File::class, 'brand_id'); // AJUSTA SI ES OTRO
}

public function notes()
{
    return $this->hasMany(Note::class, 'brand_id');
}
}



