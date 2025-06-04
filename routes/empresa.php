<?php

use App\Http\Controllers\EmpresaController;

Route::prefix('empresas')->group(function () {
    Route::get('/autocomplete', [EmpresaController::class, 'autocomplete'])->name('empresas.autocomplete');
    Route::post('/store', [EmpresaController::class, 'store'])->name('empresas.store');
});
