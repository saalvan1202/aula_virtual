<?php

use App\Http\Controllers\TipoAmbienteController;
use App\Http\Controllers\TipoMatriculaController;

Route::prefix('tipo_ambientes')->middleware('auth.permission')->group(function () {
    Route::get('/', [TipoAmbienteController::class, 'index'])->name('sedes.index');
    Route::post('/', [TipoAmbienteController::class, 'store'])->name('sedes.store');
    Route::get('{id}/edit', [TipoAmbienteController::class, 'edit'])->name('sedes.edit');
    Route::delete('/{id}', [TipoAmbienteController::class, 'destroy'])->name('sedes.destroy');
    Route::get('datatables', [TipoAmbienteController::class, 'dataTable'])->name('sedes.datatables');
});
Route::prefix('tipo_matriculas')->middleware('auth.permission')->group(function () {
    Route::get('/', [TipoMatriculaController::class, 'index'])->name('sedes.index');
    Route::post('/', [TipoMatriculaController::class, 'store'])->name('sedes.store');
    Route::get('{id}/edit', [TipoMatriculaController::class, 'edit'])->name('sedes.edit');
    Route::delete('/{id}', [TipoMatriculaController::class, 'destroy'])->name('sedes.destroy');
    Route::get('datatables', [TipoMatriculaController::class, 'dataTable'])->name('sedes.datatables');
});
