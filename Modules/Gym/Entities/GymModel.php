<?php

namespace Modules\Gym\Entities;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

abstract class GymModel extends Model
{
    // Los movimientos se guardan en UTC aunque la aplicación use hora de Honduras.
    public function freshTimestamp()
    {
        return Carbon::now('UTC');
    }

    protected function asDateTime($value)
    {
        if ($value instanceof DateTimeInterface || is_numeric($value)) {
            return parent::asDateTime($value);
        }
        return Carbon::parse($value, 'UTC');
    }
}
