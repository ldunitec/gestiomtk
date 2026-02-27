<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintHistory extends Model
{
    protected $fillable = ['user_id','card_design_id','total','person_ids','type'];
    protected $casts = ['person_ids'=>'array'];

    public function user(){ return $this->belongsTo(User::class); }
    public function design(){ return $this->belongsTo(CardDesign::class,'card_design_id'); }
}

