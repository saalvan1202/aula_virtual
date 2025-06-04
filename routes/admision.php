<?php

use App\Http\Controllers\ModalidadesAdmisionController;
use App\Http\Controllers\ModalidadesProcesoAdmisionController;
use App\Http\Controllers\ProcesosAdmisionController;
use App\Http\Controllers\RequisitosAdmisionController;
use App\Models\ModalidadProcesoAdmision;

Route::prefix('requisitos_admision')->middleware('auth.permission:modalidades_admision')->group(function () {
    Route::get('/', [RequisitosAdmisionController::class, 'index'])->name('sedes.index');
    Route::post('/', [RequisitosAdmisionController::class, 'store'])->name('sedes.store');
    Route::get('{id}/edit', [RequisitosAdmisionController::class, 'edit'])->name('sedes.edit');
    Route::delete('/{id}', [RequisitosAdmisionController::class, 'destroy'])->name('sedes.destroy');
    Route::get('datatables', [RequisitosAdmisionController::class, 'dataTable'])->name('sedes.datatables');
    Route::get('/filterModalidad/{tipo}', [RequisitosAdmisionController::class, 'filterModalidad'])->name('sedes.store');
});
Route::prefix('modalidades_admision')->middleware('auth.permission')->group(function () {
    Route::get('/', [ModalidadesAdmisionController::class, 'index'])->name('sedes.index');
    Route::post('/', [ModalidadesAdmisionController::class, 'store'])->name('sedes.store');
    Route::get('{id}/edit', [ModalidadesAdmisionController::class, 'edit'])->name('sedes.edit');
    Route::delete('/{id}', [ModalidadesAdmisionController::class, 'destroy'])->name('sedes.destroy');
    Route::get('datatables', [ModalidadesAdmisionController::class, 'dataTable'])->name('sedes.datatables');
    Route::get('datatables/requisitos/{id}', [ModalidadesAdmisionController::class, 'dataTableRequisitos'])->name('sedes.datatables'); 
   
});
Route::prefix('procesos_admision')->middleware('auth.permission')->group(function () {
    Route::get('/', [ProcesosAdmisionController::class, 'index'])->name('sedes.index');
    Route::post('/', [ProcesosAdmisionController::class, 'store'])->name('sedes.store');
    Route::get('{id}/edit', [ProcesosAdmisionController::class, 'edit'])->name('sedes.edit');
    Route::delete('/{id}', [ProcesosAdmisionController::class, 'destroy'])->name('sedes.destroy');
    Route::get('datatables', [ProcesosAdmisionController::class, 'dataTable'])->name('sedes.datatables');
    Route::get('datatables_detalle/{id}', [ProcesosAdmisionController::class, 'dataTableDetalle'])->name('sedes.datatables');
});
Route::prefix('procesos_detalle')->middleware('auth.permission:procesos_admision')->group(function () {
    Route::get('/', [ModalidadesProcesoAdmisionController::class, 'index'])->name('sedes.index');
    Route::post('/', [ModalidadesProcesoAdmisionController::class, 'store'])->name('sedes.store');
    Route::get('{id}/edit', [ModalidadesProcesoAdmisionController::class, 'edit'])->name('sedes.edit');
    Route::delete('/{id}', [ModalidadesProcesoAdmisionController::class, 'destroy'])->name('sedes.destroy');
    Route::get('datatables', [ModalidadesProcesoAdmisionController::class, 'dataTable'])->name('sedes.datatables');
   
});

