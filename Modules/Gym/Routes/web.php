<?php

use Illuminate\Support\Facades\Route;
use Modules\Gym\Http\Controllers\GymController;

Route::get('/', [GymController::class, 'index'])->middleware('can:gym.ver')->name('index');
Route::middleware('can:gym.administrar')->group(function () {
    Route::get('/socios', [GymController::class, 'socios'])->name('socios');
    Route::post('/socios', [GymController::class, 'guardarSocio'])->name('socios.guardar');
    Route::get('/socios/{socio}', [GymController::class, 'socio'])->name('socios.ver');
    Route::put('/socios/{socio}', [GymController::class, 'guardarSocio'])->name('socios.actualizar');
    Route::get('/planes', [GymController::class, 'planes'])->name('planes');
    Route::post('/planes', [GymController::class, 'guardarPlan'])->name('planes.guardar');
    Route::put('/planes/{plan}', [GymController::class, 'guardarPlan'])->name('planes.actualizar');
    Route::post('/socios/{socio}/membresias', [GymController::class, 'membresia'])->name('membresias');
    Route::post('/membresias/{membresia}/pagos', [GymController::class, 'pago'])->name('pagos');
    Route::post('/membresias/{membresia}/anular', [GymController::class, 'anular'])->name('anular');
});
Route::middleware('can:gym.accesos')->group(function () {
    Route::get('/accesos', [GymController::class, 'accesos'])->name('accesos');
    Route::post('/accesos', [GymController::class, 'registrar'])->name('accesos.registrar');
});
Route::get('/reportes', [GymController::class, 'reportes'])->middleware('can:gym.reportes')->name('reportes');
