<?php

use App\Http\Controllers\HorarioController;

Route::prefix('horarios')->middleware('auth.permission')->group(function () {
    Route::get('/', [HorarioController::class, 'index'])->name('horarios.index');
    Route::get('create', [HorarioController::class, 'create'])->name('horarios.create');
    Route::post('/', [HorarioController::class, 'store'])->name('horarios.store');
    Route::get('edit/{id}', [HorarioController::class, 'edit'])->name('horarios.edit');
    Route::delete('/{id}', [HorarioController::class, 'destroy'])->name('horarios.destroy');
    Route::get('datatables', [HorarioController::class, 'dataTable'])->name('horarios.datatables');

    Route::patch('update/{id}', [HorarioController::class, 'patchHorario'])->name('horarios.update');
    Route::post('store-horario', [HorarioController::class, 'storeHorario'])->name('horarios.storeHorario');
    Route::post('/store-curso-horario', [HorarioController::class, 'storeCursoDocenteAndHorarios'])->name('horarios.storeCursoDocenteAndHorarios');
    Route::post('/verify-curso-docente', [HorarioController::class, 'verifyCursoDocente'])->name('horarios.verifyCursoDocente');
    Route::post('/check-horario-conflict', [HorarioController::class, 'checkHorarioConflict'])->name('horarios.checkHorarioConflict');
});
