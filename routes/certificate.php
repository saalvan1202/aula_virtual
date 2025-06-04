<?php

use App\Http\Controllers\CertificadoModularController;

Route::prefix('certificados_modulares')->middleware('auth.permission')->group(function () {
    Route::get('/', [CertificadoModularController::class,'index'])->name('certificados_modulares.index');
    Route::post('/', [CertificadoModularController::class,'store'])->name('certificados_modulares.store');
    Route::get('modulos',[CertificadoModularController::class,'module'])->name('certificados_modulares.modules');
    Route::get('{id}/edit', [CertificadoModularController::class, 'edit'])->name('certificados_modulares.edit');
    Route::get('{id}/print', [CertificadoModularController::class, 'print'])->name('certificados_modulares.print');
    Route::delete('/{id}', [CertificadoModularController::class, 'destroy'])->name('certificados_modulares.destroy');
    Route::get('datatables', [CertificadoModularController::class, 'dataTable'])->name('certificados_modulares.datatables');
});
