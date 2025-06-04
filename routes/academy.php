<?php

use App\Http\Controllers\AmbienteController;
use App\Http\Controllers\CompetenciaController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\InstitutoController;
use App\Http\Controllers\PeriodoClaseController;
use App\Http\Controllers\PlanEstudioController;
use App\Http\Controllers\PlanEstudioModuloController;
use App\Http\Controllers\ProgramaEstudioController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\IndicadorCompetenciaController;
use App\Http\Controllers\SeccionController;

Route::prefix('institutos')->middleware('auth.permission')->group(function () {
    Route::get('/', [InstitutoController::class, 'index'])->name('institutos.index');
    Route::post('/', [InstitutoController::class, 'store'])->name('institutos.store');
    Route::get('{id}/edit', [InstitutoController::class, 'edit'])->name('institutos.edit');
    // Route::delete('/{id}', [InstitutoController::class, 'destroy'])->name('institutos.destroy');
    Route::get('datatables', [InstitutoController::class, 'dataTable'])->name('institutos.datatables');
});
Route::prefix('sedes')->middleware('auth.permission')->group(function () {
    Route::get('/', [SedeController::class, 'index'])->name('sedes.index');
    Route::post('/', [SedeController::class, 'store'])->name('sedes.store');
    Route::get('{id}/edit', [SedeController::class, 'edit'])->name('sedes.edit');
    Route::delete('/{id}', [SedeController::class, 'destroy'])->name('sedes.destroy');
    Route::get('datatables', [SedeController::class, 'dataTable'])->name('sedes.datatables');
});
Route::prefix('programas_estudios')->middleware('auth.permission')->group(function () {
    Route::get('/', [ProgramaEstudioController::class, 'index'])->name('programas_estudios.index');
    Route::post('/', [ProgramaEstudioController::class, 'store'])->name('programas_estudios.store');
    Route::get('{id}/edit', [ProgramaEstudioController::class, 'edit'])->name('programas_estudios.edit');
    Route::delete('/{id}', [ProgramaEstudioController::class, 'destroy'])->name('programas_estudios.destroy');
    Route::get('datatables', [ProgramaEstudioController::class, 'dataTable'])->name('programas_estudios.datatables');

    Route::get('by-sede/{id_sede}', [ProgramaEstudioController::class, 'getProgramaEstudioBySede'])->name('programas_estudios.bySede');
});
Route::prefix('planes_estudios')->middleware('auth.permission')->group(function () {
    Route::get('/', [PlanEstudioController::class, 'index'])->name('planes_estudios.index');
    Route::get('/{id}/modulos',[PlanEstudioController::class,'modules'])->name('planes_estudios.modules');
    Route::post('/', [PlanEstudioController::class, 'store'])->name('planes_estudios.store');
    Route::get('{id}/edit', [PlanEstudioController::class, 'edit'])->name('planes_estudios.edit');
    Route::delete('/{id}', [PlanEstudioController::class, 'destroy'])->name('planes_estudios.destroy');
    Route::get('datatables', [PlanEstudioController::class, 'dataTable'])->name('planes_estudios.datatables');
});
Route::prefix('planes_estudios')->middleware('auth.permission:aula-virtual')->group(function () {
    Route::get('alumno/plan-estudios', [PlanEstudioController::class, 'getPlanEstudiante'])
        ->name('planes_estudios.getPlanEstudiante');
});
Route::prefix('planes_estudios')->middleware('auth.permission:horarios')->group(function () {
    Route::get('by-programa/{id_programa_estudio}', [PlanEstudioController::class, 'getPlanEstudioByPrograma'])
        ->name('planes_estudios.byPrograma');
});
Route::prefix('planes_estudios_modulos')->middleware('auth.permission:planes_estudios')->group(function () {
    Route::get('/{idPlan}/combos',[PlanEstudioModuloController::class,'combo'])->name('planes_estudios_modulos.combo');
    Route::post('/', [PlanEstudioModuloController::class, 'store'])->name('planes_estudios_modulos.store');
    Route::get('{id}/edit', [PlanEstudioModuloController::class, 'edit'])->name('planes_estudios_modulos.edit');
    Route::delete('/{id}', [PlanEstudioModuloController::class, 'destroy'])->name('planes_estudios_modulos.destroy');
    Route::get('/{id}/datatables', [PlanEstudioModuloController::class, 'dataTable'])->name('planes_estudios_modulos.datatables');
});
Route::prefix('competencias')->middleware('auth.permission:planes_estudios')->group(function () {
    Route::get('/{idPlan}/datatables', [CompetenciaController::class, 'dataTable'])->name('competencias.datatables');
    Route::get('/{idPlan}/autocomplete', [CompetenciaController::class, 'autocomplete'])->name('competencias.autocomplete');
    Route::post('/', [CompetenciaController::class, 'store'])->name('competencias.store');
    Route::get('{id}/edit', [CompetenciaController::class, 'edit'])->name('competencias.edit');
    Route::delete('/{id}', [CompetenciaController::class, 'destroy'])->name('competencias.destroy');
});
Route::prefix('indicadores_competencias')->middleware('auth.permission:planes_estudios')->group(function () {
    Route::get('/{idCompetencia}/datatables', [IndicadorCompetenciaController::class, 'dataTable'])->name('competencias.datatables');
    Route::post('/', [IndicadorCompetenciaController::class, 'store'])->name('competencias.store');
    Route::get('{id}/edit', [IndicadorCompetenciaController::class, 'edit'])->name('competencias.edit');
    Route::delete('/{id}', [IndicadorCompetenciaController::class, 'destroy'])->name('competencias.destroy');
});
Route::prefix('cursos')->middleware('auth.permission:planes_estudios')->group(function () {
    Route::get('/{idPlan}/datatables', [CursoController::class, 'dataTable'])->name('cursos.datatables');
    Route::get('/{idPlan}/autocomplete', [CursoController::class, 'autocomplete'])->name('cursos.autocomplete');
    Route::post('/', [CursoController::class, 'store'])->name('cursos.store');
    Route::get('{id}/edit', [CursoController::class, 'edit'])->name('cursos.edit');
    Route::delete('/{id}', [CursoController::class, 'destroy'])->name('cursos.destroy');

    Route::get('by-plan-ciclo/{id_plan_estudio}/{id_ciclo}', [CursoController::class, 'getCursoByPlanEstudioAndCiclo'])->name('cursos.byPlanCiclo');
});
Route::prefix('ambientes')->middleware('auth.permission')->group(function () {
    Route::get('/', [AmbienteController::class, 'index'])->name('programas_estudios.index');
    Route::post('/', [AmbienteController::class, 'store'])->name('programas_estudios.store');
    Route::get('{id}/edit', [AmbienteController::class, 'edit'])->name('programas_estudios.edit');
    Route::delete('/{id}', [AmbienteController::class, 'destroy'])->name('programas_estudios.destroy');
    Route::get('datatables', [AmbienteController::class, 'dataTable'])->name('programas_estudios.datatables');
    Route::get('by-tipo/{id_tipo_aula}', [AmbienteController::class, 'getAmbientesByTipo'])->name('ambientes.byTipo');
});
Route::prefix('periodo_clases')->middleware('auth.permission')->group(function () {
    Route::get('/', [PeriodoClaseController::class, 'index'])->name('periodo_clases.index');
    Route::post('/', [PeriodoClaseController::class, 'store'])->name('periodo_clases.store');
    Route::get('{id}/edit', [PeriodoClaseController::class, 'edit'])->name('periodo_clases.edit');
    Route::delete('/{id}', [PeriodoClaseController::class, 'destroy'])->name('periodo_clases.destroy');
    Route::get('datatables', [PeriodoClaseController::class, 'dataTable'])->name('periodo_clases.datatables');
});
Route::prefix('seccion')->middleware('auth.permission')->group(function () {
    Route::get('/', [SeccionController::class, 'index'])->name('seccion.index');
    Route::post('/', [SeccionController::class, 'store'])->name('seccion.store');
    Route::get('{id}/edit', [SeccionController::class, 'edit'])->name('seccion.edit');
    Route::delete('/{id}', [SeccionController::class, 'destroy'])->name('seccion.destroy');
    Route::get('datatables', [SeccionController::class, 'dataTable'])->name('seccion.datatables');
});
Route::prefix('aula-virtual')->middleware('auth.permission')->group(function () {
    Route::get('/', [EvaluacionController::class, 'index'])->name('periodo_clases.index');
    Route::get('nota/', [EvaluacionController::class, 'indexNotas'])->name('periodo_clases.index');
    Route::get('curso-docente/{uuid}', [EvaluacionController::class, 'cursoDocente'])->name('periodo_clases.index');
    Route::post('/', [EvaluacionController::class, 'store'])->name('periodo_clases.store');
    Route::get('{id}/edit', [PeriodoClaseController::class, 'edit'])->name('periodo_clases.edit');
    Route::delete('/{id}', [PeriodoClaseController::class, 'destroy'])->name('periodo_clases.destroy');
    Route::get('datatables', [PeriodoClaseController::class, 'dataTable'])->name('periodo_clases.datatables');
    Route::get('/{periodo}', [EvaluacionController::class, 'getCursos'])->name('periodo_clases.destroy');
});
