<?php

use Illuminate\Support\Facades\Route;
use Modules\ActividadesTecnicas\Http\Controllers\{DashboardController, WorkOrderController};

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/ordenes', [WorkOrderController::class, 'index'])->name('ordenes.index');
Route::middleware('can:actividades-tecnicas.gestionar')->group(function () {
    Route::post('/desde-agenda/{agenda}', [WorkOrderController::class, 'fromAgenda'])->name('ordenes.desde-agenda');
    Route::get('/ordenes/create', [WorkOrderController::class, 'create'])->name('ordenes.create');
    Route::post('/ordenes', [WorkOrderController::class, 'store'])->name('ordenes.store');
    Route::get('/ordenes/{workOrder}/edit', [WorkOrderController::class, 'edit'])->name('ordenes.edit');
    Route::put('/ordenes/{workOrder}', [WorkOrderController::class, 'update'])->name('ordenes.update');
});
Route::delete('/ordenes/{workOrder}', [WorkOrderController::class, 'destroy'])
    ->middleware('can:actividades-tecnicas.cancelar')->name('ordenes.destroy');
Route::get('/ordenes/{workOrder}', [WorkOrderController::class, 'show'])->name('ordenes.show');

