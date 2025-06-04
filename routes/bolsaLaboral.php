<?php

use App\Http\Controllers\BolsaLaboralController;
use App\Http\Controllers\OfertaLaboralController;

Route::prefix('bolsa_laborales')->middleware('auth.permission')->group(function () {
    Route::get('/', [BolsaLaboralController::class, 'index'])->name('bolsaLaborales.index');
    Route::get('/create', [BolsaLaboralController::class, 'create'])->name('bolsaLaborales.create');
    Route::patch('/update/{id}', [BolsaLaboralController::class, 'update'])->name('bolsaLaborales.update');
    Route::get('edit/{id}', [BolsaLaboralController::class, 'edit'])->name('bolsaLaborales.edit');
    Route::delete('/{id}', [BolsaLaboralController::class, 'destroy'])->name('bolsaLaborales.destroy');
    Route::post('/', [BolsaLaboralController::class, 'store'])->name('bolsaLaborales.store');
    Route::get('/datatables', [BolsaLaboralController::class, 'dataTable'])->name('bolsaLaborales.dataTable');
});

Route::prefix('oferta_laboral')->middleware('auth.permission')->group(function () {
    Route::get('/', [OfertaLaboralController::class, 'index'])->name('ofertaLaboral.index');
    Route::get('/{uuid}', [OfertaLaboralController::class, 'getById'])->name('ofertaLaboral.getById');
    Route::post('/', [OfertaLaboralController::class, 'index'])->name('ofertaLaboral.filtros');
});
