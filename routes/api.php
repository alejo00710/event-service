<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index']); // Listar todos los eventos (público)
    Route::get('/{id}', [EventController::class, 'show']); // Ver detalle de evento

    Route::post('/', [EventController::class, 'store']); // Crear evento (admin)
    Route::put('/{id}', [EventController::class, 'update']); // Actualizar evento (admin)
    Route::delete('/{id}', [EventController::class, 'destroy']); // Eliminar evento (admin)
});
