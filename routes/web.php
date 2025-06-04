<?php

use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\UbigeoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::prefix('ubigeos')->group(function () {
       Route::get('/departamentos',[UbigeoController::class, 'department'])->name('ubigeos.department');
       Route::get('/provincias',[UbigeoController::class, 'province'])->name('ubigeos.province');
       Route::get('/distritos',[UbigeoController::class, 'district'])->name('ubigeos.district');
    });
    Route::prefix('archivos')->group(function(){
        Route::get('/descargar/{id}/{archivo}',[ArchivoController::class,'download'])->name('archivos.download');
    });
    Route::get('/ruc/{numero}',function($numero){
        $api=new \App\Services\RucTecnotrends();
        $api->get($numero);
        if($api->getError()){
            return response()->json($api->getError(),404);
        }
        return response()->json($api->result());
    });
    Route::get('admin/logs',[LogViewerController::class,'index']);
    require __DIR__.'/security.php';
    require __DIR__.'/academy.php';
    require __DIR__.'/mantenimiento.php';
    require __DIR__.'/horario.php';
    require __DIR__.'/admision.php';
    require __DIR__.'/teacher.php';
    require __DIR__.'/matricula.php';
    require __DIR__.'/bolsaLaboral.php';
    require __DIR__.'/certificate.php';
    require __DIR__.'/black-board.php';
    require __DIR__.'/empresa.php';
});

Route::prefix('archivos')->group(function(){
    Route::get('/mostrar/{id}/{archivo}',[ArchivoController::class,'preview'])->name('archivos.preview');
});
Auth::routes([
    'confirm'=>false
]);
Route::get('/register', [RegisterController::class, 'index'])->name('register.index');
Route::get('/continuar-registro/{token}', [RegisterController::class, 'continuarRegistro'])->name('registro.continuar-registro');
Route::post('/finalizar-registro/{token}', [RegisterController::class, 'finalizarRegistro'])->name('registro.finalizar-registro');
Route::post('/registro/verificar', [RegisterController::class, 'verificarDniEmail'])->name('registro.verificar');

