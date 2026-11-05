<?php

use Illuminate\Support\Facades\Route;
use Modules\Mikrotik\Http\Controllers\MikrotikController as C;

Route::get('/', [C::class, 'index'])->middleware('can:mikrotik.ver')->name('index');
Route::middleware('can:mikrotik.configurar')->group(function () {
    Route::get('/configuracion', [C::class, 'configuracion'])->name('configuracion');
    Route::post('/routers', [C::class, 'guardar'])->name('guardar');
    Route::put('/routers/{router}', [C::class, 'guardar'])->name('actualizar');
    Route::post('/routers/{router}/probar', [C::class, 'probar'])->name('probar');
    Route::get('/routers/{router}/preparacion', [C::class, 'preparacion'])->name('preparacion');
    Route::post('/routers/{router}/preparar', [C::class, 'preparar'])->name('preparar');
});
Route::middleware('can:mikrotik.ver')->group(function () {
    Route::get('/routers/{router}/estado', [C::class, 'estado'])->name('estado');
});
Route::middleware('can:mikrotik.fichas')->group(function () {
    Route::get('/empleados', [C::class, 'empleados'])->name('empleados');
    Route::post('/empleados', [C::class, 'crearEmpleado'])->name('empleados.crear');
    Route::get('/fichas', [C::class, 'fichas'])->name('fichas');
    Route::post('/fichas', [C::class, 'crear'])->name('crear');
    Route::get('/lotes/{lote}', [C::class, 'lote'])->name('lote');
    Route::post('/lotes/{lote}/publicar', [C::class, 'publicar'])->name('publicar');
    Route::get('/lotes/{lote}/imprimir', [C::class, 'imprimir'])->name('imprimir');
    Route::post('/fichas/{ficha}/revocar', [C::class, 'revocar'])->name('revocar');
    Route::post('/routers/{router}/conciliar', [C::class, 'conciliar'])->name('conciliar');
});
Route::middleware('can:mikrotik.sesiones')->group(function () {
    Route::get('/sesiones', [C::class, 'elegirSesiones'])->name('sesiones.routers');
    Route::get('/routers/{router}/sesiones', [C::class, 'sesiones'])->name('sesiones');
    Route::post('/fichas/{ficha}/desconectar', [C::class, 'desconectar'])->name('desconectar');
});

