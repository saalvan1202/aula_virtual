<?php

namespace App\Http\Middleware;

use App\Models\Modulo;
use Closure;
use Illuminate\Http\Request;

class PermisoMiddleware
{

    public function handle(Request $request, Closure $next,$modulo=null)
    {
        if($modulo==null){
            $modulo=ltrim($request->route()->getPrefix(),'/');
        }
        $modulo=explode(',',$modulo);
        $allPermisos=$this->getPermisos();
        $hasModulo=$allPermisos->whereIn('ruta',$modulo)->first();
        if($hasModulo==null){
            /*if ($request->header('X-Inertia')){
                return redirect()->to('/403');
            }*/
            if($request->expectsJson()){
                return response()->json('Acceso no autorizado.', 403);
            }
            abort(403, 'Acceso no autorizado.');
        }
        return $next($request);
    }
    public function getPermisos()
    {
        $allPermisos=session('permisos');
        if($allPermisos==null){
            $allPermisos=Modulo::getPermisos(auth()->user()->id_perfil);
            session()->put('permisos',$allPermisos);
        }
        return collect($allPermisos);
    }

}
