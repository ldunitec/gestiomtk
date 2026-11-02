<?php

namespace Modules\Gym\Entities;

class Pago extends GymModel
{
    protected $table = 'gym_pagos';
    protected $guarded = ['id'];
    protected $casts = [];

}
