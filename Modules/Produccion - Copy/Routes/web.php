<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Modules\Produccion\Http\Controllers\AgendaController;
use Modules\Produccion\Http\Controllers\Agendav2Controller;
use Illuminate\Support\Facades\Route;
use Modules\Produccion\Http\Controllers\TaskController;

Route::prefix('produccion')->group(function () {
    Route::get('/', 'ProduccionController@index');
});

Route::middleware(['auth'])
    ->prefix('produccion/admin')
    ->as('produccion.admin.')
    ->group(function () {
        Route::post('agendas/{event}/update-date', [AgendaController::class, 'updateDate'])->name('agendas.updateDate');
        Route::get('/agendas/get-events', [AgendaController::class, 'getEvents'])->name('agendas.getEvents');
        Route::post('agendas/{event}/update-item', [AgendaController::class, 'updateDateItem'])->name('agendas.updateDateItem');
        // Route::post('agendas/{event}/update-item', [AgendaController::class, 'updateDateItem'])->name('agendas.updateDateItem');
        Route::post('agendas/{event}/update-estate', [AgendaController::class, 'updateEstado'])->name('agendas.updateEstado');
        Route::put('agendas/completar/{id}', [AgendaController::class, 'completar'])->name('agendas.completar');
        Route::put('agendas/updateNoAgendado/{id}', [AgendaController::class, 'updateNoAgendado'])->name('agendas.updateNoAgendado');
        Route::resource('agendas', AgendaController::class);
        Route::post('agendas/importar-excel', [AgendaController::class, 'importExcel'])->name('agenda.import');

    });

// Route::middleware(['auth'])->group(function () {



//    ->middleware('auth', 'can:admin.agenda.completar');
//     Route::delete('/admin/agenda/{id}', [AgendaController::class, 'destroy'])->name('admin.agenda.destroy')->middleware('auth', 'can:admin.agenda.destroy');

//    ->middleware('auth', 'can:admin.agenda.updateDate');
//   ->middleware('auth', 'can:admin.agenda.updateEstado');
// });





// Agrupamos las rutas bajo un prefijo para mantener el orden
Route::prefix('produccion/admin')->group(function () {

    // Ruta para mostrar el formulario (opcional)
    Route::get('/tareas/crear', [TaskController::class, 'create'])->name('produccion.admin.tasks.create');

    // Ruta para procesar el envío a Todoist
    Route::post('/tareas/store', [TaskController::class, 'store'])->name('produccion.admin.tasks.store');

});