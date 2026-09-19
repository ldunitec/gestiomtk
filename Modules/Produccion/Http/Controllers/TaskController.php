<?php

namespace Modules\Produccion\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Produccion\Services\TodoistService;

class TaskController extends Controller
{protected $todoist;

    public function __construct(TodoistService $todoist)
    {
        $this->todoist = $todoist;
    }

      public function create()
    {
return view('produccion::admin.tasks.create');
    }


public function store(TodoistService $todoist)
{
    $task = $todoist->createTask([
        'title' => 'Tarea desde Laravel',
        'description' => 'Creada automáticamente',
        'due' => 'tomorrow',
        'project_id' => 2169789392
    ]);

    return response()->json($task);
}
}