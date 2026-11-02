<?php

use Illuminate\Support\Facades\Route;
use Modules\Gym\Http\Controllers\GymController;

Route::post('/validar', [GymController::class, 'validarApi'])->name('validar');
Route::post('/accesos', [GymController::class, 'registrarApi'])->name('accesos');
