<?php

namespace Modules\Produccion\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

   protected $fillable = ['title', 'description', 'todoist_project_id', 'todoist_assignee_id'];
    
  
}
