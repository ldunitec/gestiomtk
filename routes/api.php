<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// tareas 


// Route::get('/admin/tasks', [TaskController::class, 'index']);
// Route::post('/admin/tasks', [TaskController::class, 'store']);
// Route::put('/admin/tasks/{task}', [TaskController::class, 'update']);
// Route::delete('/admin/tasks/{task}', [TaskController::class, 'destroy']);
// Route::post('/admin/tasks/sync', [TaskController::class, 'syncTodoist']);

Route::apiResource('tasks', TaskController::class);
Route::post('tasks/sincronizar', [TaskController::class, 'sincronizar']);

