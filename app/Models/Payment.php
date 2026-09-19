<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'debt_id',
        'payment_date',
        'amount',
        'comment'
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}
