<?php

use App\Http\Controllers\ModuloController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\UsuarioController;

Route::prefix('modulos')->group(function () {
    Route::get('/', [ModuloController::class, 'index'])->name('modulos.index');
    Route::post('/', [ModuloController::class, 'store'])->name('modulos.store');
    Route::get('{id}/edit', [ModuloController::class, 'edit'])->name('modulos.edit');
    Route::delete('/{id}', [ModuloController::class, 'destroy'])->name('modulos.destroy');
    Route::get('datatables', [ModuloController::class, 'dataTable'])->name('modulos.datatables');
    Route::get('/dependencia',[ModuloController::class, 'parent'])->name('modulos.parent');
});
Route::prefix('permisos')->group(function () {
    Route::get('/', [PermisoController::class, 'index'])->name('permisos.index');
    Route::post('/', [PermisoController::class, 'store'])->name('permisos.store');
    Route::get('{id}/edit', [PermisoController::class, 'edit'])->name('permisos.edit');
});
Route::prefix('usuarios')->middleware('auth.permission')->group(function () {
    Route::get('/', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::delete('/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    Route::get('datatables', [UsuarioController::class, 'dataTable'])->name('usuarios.datatables');
});
Route::prefix('usuarios')->group(function () {
    Route::get('/cuenta', [UsuarioController::class, 'account'])->name('usuarios.account');
    Route::post('cuenta/cambiar',[UsuarioController::class, 'changeAccount'])->name('usuarios.account.change');
    Route::post('cuenta/clave',[UsuarioController::class, 'changePassword'])->name('usuarios.account.changePassword');
});
Route::prefix('usuarios')->middleware(['auth.permission:docentes,usuarios,estudiantes'])->group(function () {
    Route::get('/buscar/dni/{dni}',[UsuarioController::class,'searchExternalDni'])->name('usuarios.search.dni');
});
Route::prefix('notificaciones')->group(function () {
    Route::get('/', [NotificacionesController::class, 'getNotificaciones'])->name('notificaciones.getNotificaciones');
});
