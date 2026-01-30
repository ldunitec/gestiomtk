<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'name','background','width','height'
    ];

    public function elements()
    {
        return $this->hasMany(TemplateElement::class);
    }
}
