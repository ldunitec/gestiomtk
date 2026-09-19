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

use Illuminate\Support\Facades\Route;
use Modules\Documentacion\Http\Controllers\DocumentacionController;

// Route::prefix('documentacion')->group(function() {
//     Route::get('/', 'DocumentacionController@index');
// });

Route::prefix('documentacion/admin/documentaciones')->as('documentacion.admin.documentaciones.')->group(function () {

    Route::get('/',  [DocumentacionController::class, 'index'])->name('index');
    Route::get('/create',  [DocumentacionController::class,'create'])->name('create');
    Route::post('/store', [DocumentacionController::class,'store'])->name('store');
    Route::get('/{id}/edit', [DocumentacionController::class,'edit'])->name('edit');

    Route::post('/seccion', [DocumentacionController::class,'seccion'])->name('seccion');

    Route::get('/pdf/{id}', [DocumentacionController::class,'pdf'])->name('pdf');

});