<?php

namespace Modules\Gym\Entities;

class Plan extends GymModel
{
    protected $table = 'gym_planes';
    protected $guarded = ['id'];
    protected $casts = ['activo' => 'boolean'];

}
