<?php

namespace Modules\Mikrotik\Entities;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

abstract class Modelo extends Model
{
    protected $guarded = ['id'];

    public function freshTimestamp()
    {
        return Carbon::now('UTC');
    }

    protected function asDateTime($value)
    {
        return $value instanceof DateTimeInterface || is_numeric($value)
            ? parent::asDateTime($value) : Carbon::parse($value, 'UTC');
    }
}
