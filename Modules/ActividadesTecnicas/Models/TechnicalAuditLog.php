<?php

namespace Modules\ActividadesTecnicas\Models;

class TechnicalAuditLog extends Modelo
{
    protected $table = 'at_technical_audit_logs';
    public $timestamps = false;
    protected $fillable = ['user_id','action','subject_type','subject_id','old_values','new_values','created_at'];
    protected $casts = ['old_values'=>'array','new_values'=>'array','created_at'=>'datetime'];
}
