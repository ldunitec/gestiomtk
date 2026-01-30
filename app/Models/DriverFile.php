<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverFile extends Model
{
    protected $fillable = [
        'model_id',
        'type',
        'file_path',
        'drive_path'
    ];

    public function deviceModel()
    {
        return $this->belongsTo(DeviceModel::class, 'model_id');
    }
}
