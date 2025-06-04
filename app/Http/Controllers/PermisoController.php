<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use App\Models\Perfil;
use App\Models\Permiso;
use App\Services\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PermisoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.permission:permisos')->only('index');
    }
    public function index()
    {
        return Inertia::render('Security/Permission',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'perfiles'=>Perfil::where('estado','A')->get()
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id_perfil'=>'required',
            'modulos'=>'required|array'
        ]);

        return DB::transaction(function() use ($request){

            foreach($request->input('modulos') as $value){
                $obj=Permiso::where('id_modulo',$value['id'])
                    ->where('id_perfil',$request->input('id_perfil'))
                    ->first();
                if(is_null($obj)){
                    $obj=new Permiso();
                }
                $obj->id_modulo=$value['id'];
                $obj->id_perfil=$request->input('id_perfil');
                $obj->estado=$value['estado'];
                $obj->save();
            }
            $request->session()->forget('menus');
            $request->session()->forget('permisos');
            return response()->json('ok');
        });
    }
    public function edit($idPerfil,Request $request)
    {
        $modulos=(new Modulo())->getTree($idPerfil);
        return response()->json((new Menu)->getMenus(collect($modulos)));
    }
}
