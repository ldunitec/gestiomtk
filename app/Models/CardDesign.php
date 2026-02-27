<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardDesign extends Model
{
    protected $fillable = ['name','layout'];
    protected $casts = ['layout'=>'array',  'design_json' => 'array',];




}

