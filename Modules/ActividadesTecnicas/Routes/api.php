<?php

use Illuminate\Support\Facades\Route;
use Modules\ActividadesTecnicas\Http\Controllers\DashboardController;

Route::get('/health', [DashboardController::class, 'health'])->name('health');
