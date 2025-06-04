<?php

use App\Http\Controllers\AmbienteController;
use App\Http\Controllers\ApoderadoController;
use App\Http\Controllers\CompetenciaController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\InstitutoController;
use App\Http\Controllers\MatriculasController;
use App\Http\Controllers\PlanEstudioController;
use App\Http\Controllers\PlanEstudioModuloController;
use App\Http\Controllers\ProgramaEstudioController;
use App\Http\Controllers\SedeController;

Route::prefix('estudiantes')->middleware('auth.permission')->group(function () {
    Route::get('/', [EstudianteController::class, 'index'])->name('estudiantes.index');
    Route::get('/programasFilter/{id}', [EstudianteController::class, 'getProgramaSedes'])->name('estudiantes.index');
    Route::get('/planesFilter/{id}', [EstudianteController::class, 'getPlanProgramas'])->name('estudiantes.index');
    Route::get('/distritos/{id}', [EstudianteController::class, 'getDistritos'])->name('estudiantes.index');
    Route::post('/', [EstudianteController::class, 'store'])->name('estudiantes.store');
    Route::delete('/{id}', [EstudianteController::class, 'destroy'])->name('estudiantes.destroy');
    Route::get('{id}/edit', [EstudianteController::class, 'edit'])->name('estudiantes.edit');
    Route::get('datatables', [EstudianteController::class, 'dataTable'])->name('estudiantes.datatables');
    Route::post('/importar-estudiantes-excel', [EstudianteController::class, 'import']);
});
Route::prefix('matriculas')->middleware('auth.permission')->group(function () {
    Route::get('/', [MatriculasController::class, 'index'])->name('matriculas.index');
    Route::get('/programasFilter/{id}', [EstudianteController::class, 'getProgramaSedes'])->name('matriculas.index');
    Route::get('/planesFilter/{id}', [EstudianteController::class, 'getPlanProgramas'])->name('matriculas.index');
    Route::get('/autocomplete', [MatriculasController::class, 'autocomplete'])->name('matriculas.autocomplete');
    Route::post('/searchCursos', [MatriculasController::class, 'searchCursos'])->name('matriculas.autocomplete');
    Route::post('/', [MatriculasController::class, 'store'])->name('matriculas.store');
    Route::get('{id}/edit', [MatriculasController::class, 'edit'])->name('matriculas.edit');
    Route::delete('/{id}', [MatriculasController::class, 'destroy'])->name('matriculas.destroy');
    Route::get('datatables/{id_sede}', [MatriculasController::class, 'dataTable'])->name('matriculas.datatables');
});

Route::prefix('apoderados')->group(function () {
    Route::get('/', [ApoderadoController::class, 'index'])->name('apoderados.index');
    Route::post('/', [ApoderadoController::class, 'store'])->name('apoderados.store');
    Route::get('{id}/edit', [ApoderadoController::class, 'edit'])->name('apoderados.edit');
    Route::delete('/{id}', [ApoderadoController::class, 'destroy'])->name('apoderados.destroy');
    Route::get('/autocomplete', [ApoderadoController::class, 'autocomplete'])->name('apoderados.autocomplete');
    Route::get('datatables', [ApoderadoController::class, 'dataTable'])->name('apoderados.datatables');
});




