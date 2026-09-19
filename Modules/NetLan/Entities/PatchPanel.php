<?php

namespace Modules\NetLan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatchPanel extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
    }
}
