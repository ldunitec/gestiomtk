<?php

namespace Modules\ActividadesTecnicas\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

abstract class Modelo extends Model
{
    public function freshTimestamp() { return Carbon::now('UTC'); }

    protected function asDateTime($value)
    {
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}(\.\d+)?$/D', $value)) {
            return Carbon::parse($value, 'UTC');
        }
        return parent::asDateTime($value);
    }
}
