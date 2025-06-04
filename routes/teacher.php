<?php

use App\Http\Controllers\AsistenciaUsuarioController;
use App\Http\Controllers\DocenteController;

Route::prefix('docentes')->middleware('auth.permission')->group(function () {
    Route::get('/', [DocenteController::class,'index'])->name('docentes.index');
    Route::post('/', [DocenteController::class,'store'])->name('docentes.store');
    Route::get('{id}/edit', [DocenteController::class, 'edit'])->name('docentes.edit');
    Route::delete('/{id}', [DocenteController::class, 'destroy'])->name('docentes.destroy');
    Route::get('datatables', [DocenteController::class, 'dataTable'])->name('docentes.datatables');
});
Route::prefix('asistencias_docentes')->middleware('auth.permission')->group(function () {
    Route::get('/',[AsistenciaUsuarioController::class,'indexTeacher'])->name('asistencias_docentes.index');
    Route::get('/lista',[AsistenciaUsuarioController::class,'listTeacher'])->name('asistencias_docentes.list');
    Route::post('/',[AsistenciaUsuarioController::class,'store'])->name('asistencias_docentes.store');
});
