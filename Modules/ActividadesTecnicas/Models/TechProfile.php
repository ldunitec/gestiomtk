<?php
namespace Modules\ActividadesTecnicas\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechProfile extends Modelo
{
    protected $table = 'at_tech_profiles';
    protected $fillable = ['user_id', 'employee_code', 'specialty', 'phone', 'is_active', 'notes'];
    protected $casts = ['is_active' => 'boolean'];
    public function user(): BelongsTo { return $this->belongsTo(\App\Models\User::class); }
}
