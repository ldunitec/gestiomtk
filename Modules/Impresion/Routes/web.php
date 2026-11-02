<?php

use Illuminate\Support\Facades\Route;
use Modules\Impresion\Http\Controllers\CreyController;
use Modules\Impresion\Http\Controllers\PanelController;

Route::middleware(['auth', 'can:admin.alumnos.crey'])->prefix('impresion')->group(function () {
    Route::get('/', fn () => redirect()->route('impresion.panel'));
    Route::get('/panel', [PanelController::class, 'index'])->name('impresion.panel');
    Route::get('/fotos/{crey}', [PanelController::class, 'foto'])->whereNumber('crey')->name('impresion.foto');
    Route::post('/fotos/{crey}', [PanelController::class, 'upload'])->whereNumber('crey')->name('impresion.foto.upload');
    Route::post('/zip', [PanelController::class, 'zip'])->name('impresion.zip');
    Route::post('/preparar', [PanelController::class, 'preparar'])->name('impresion.preparar');
    Route::get('/vista/{token}', [PanelController::class, 'vista'])->whereUuid('token')->name('impresion.vista');
    Route::post('/vista/{token}/confirmar', [PanelController::class, 'confirmar'])->whereUuid('token')->name('impresion.confirmar');

    Route::prefix('admin/creys')->name('impresion.admin.creys.')->group(function () {
        Route::get('/', [CreyController::class, 'index'])->name('crey');
        Route::post('/importar', [CreyController::class, 'importar'])
            ->middleware('can:admin.alumnos.importar.crey')->name('importar');
        Route::get('/pdf', fn () => redirect()->route('impresion.panel'))->name('excel_pdf');
        Route::delete('/{crey}', [CreyController::class, 'destroy'])->whereNumber('crey')->name('destroy');
        Route::put('/{crey}', [CreyController::class, 'update'])->whereNumber('crey')->name('update');
    });
});

