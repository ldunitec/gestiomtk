<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateElement extends Model
{
    protected $fillable = [
        'template_id','type','field','x','y','font_size','color'
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
