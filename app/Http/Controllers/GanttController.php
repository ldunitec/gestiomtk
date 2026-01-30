<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarea;

class GanttController extends Controller
{
    public function index()
    {
        // Trae todas las tareas
        $tareas = Tarea::all();
        // Transformamos datos para Frappe Gantt
 $tasks = $tareas->map(function($t){
    return [
        'id' => $t->id,
        'name' => trim(preg_replace("/\r|\n/", "", $t->nombre)),
        'start' => date('Y-m-d', strtotime($t->fecha_inicio)),
        'end'   => date('Y-m-d', strtotime($t->fecha_fin)),
        'progress' => $t->progreso,
        'custom_class' => $t->estado == 'Pendiente' ? 'bar-pending' : 'bar-complete'
    ];
});

          

        return view('admin.gantt.index', compact('tasks'));
    }
}
