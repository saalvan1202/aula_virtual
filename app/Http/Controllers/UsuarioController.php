<?php

namespace App\Http\Controllers;


use App\Actions\GuardarPersona;
use App\Actions\GuardarUsuario;
use App\Http\Requests\UsuarioRequest;
use App\Models\Perfil;
use App\Models\User;
use App\Services\DniTecnotrends;
use App\Services\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class UsuarioController
{
    public function index()
    {
        return Inertia::render('Security/User', [
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'perfiles'=>Perfil::select('id','descripcion')->whereNotIn('id',Variable::isNotModuleUser())
                ->where('estado','A')->orderBy('id')->get(),
            'tipo_documentos' => DB::table('tipo_documentos_identidades')->where('estado', 'A')->orderBy('id')->get(),
            'tipo_contratos' => DB::table('tipo_contratos')->where('estado', 'A')->orderBy('id')->get(),
        ]);
    }
    public function store(UsuarioRequest $request,GuardarPersona $guardarPersona,GuardarUsuario $guardarUsuario)
    {
        set_time_limit(0);
        return DB::transaction(function () use ($request,$guardarPersona,$guardarUsuario) {
            $person=$guardarPersona->handle($request);
            $obj=$guardarUsuario->handle($person,$request);
            return response()->json($obj);
        });

    }
    public function edit($id)
    {
        $record=User::joinPersona()->where('usuarios.id',$id)->firstOrFail();
        if($record->id==Variable::IDMASTER && auth()->user()->id!=Variable::IDMASTER){
            throw ValidationException::withMessages([
                'record' => ['no puedes modificar este usuario'],
            ]);
        }
        $record->password_vista=Variable::PASSWORD;
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=User::findOrFail($id);
        if($obj->id==Variable::IDMASTER){
            throw ValidationException::withMessages([
                'record' => ['no puedes eliminar este usuario'],
            ]);
        }
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function searchExternalDni($dni)
    {
        $api=new DniTecnotrends();
        $api->get($dni);
        if($api->getError()!=null){
            return response()->json($api->getError(),404);
        }
        return response()->json($api->result());
    }
    public function account()
    {
        return Inertia::render('User/Account',[
            'user' => auth()->user()->load('perfil')->load(['persona'=>function($q){
                $q->select('id','nombres','apellido_paterno','apellido_materno','numero_documento');
            }])
        ]);
    }
    public function changeAccount(Request $request)
    {
        $request->validate([
            'nombres'=>'required|string|max:150',
            'apellido_paterno'=>'required|string|max:150',
            'apellido_materno'=>'required|string|max:150'
        ]);
        $user=auth()->user();
        $user->persona()->update(
            $request->only([
                'nombres','apellido_paterno','apellido_materno'
            ])
        );
        return response()->json('ok');
    }
    public function changePassword(Request $request){
        $request->validate([
            'current_password'=>'required|string',
            'new_password'=>'required|confirmed|string',
            'new_password_confirmation'=>'required|string'
        ],[],[
            'current_password'=>'Contraseña actual',
            'new_password'=>'Contraseña nueva',
            'new_password_confirmation'=>'Confirmar contraseña nueva'
        ]);
        $user=auth()->user();
        if (!Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['la contraseña actual no es correcta'],
            ]);
        }
        $user->password=bcrypt($request->input('new_password'));
        $user->update();

        return response()->json('ok');
    }
    public function dataTable()
    {
        $records=User::selectRaw('
                usuarios.id,p.nombres,p.apellido_paterno,p.apellido_materno,pp.descripcion as perfil,
                p.numero_documento,
                td.abreviatura as tipo_documento
            ')
            ->join('personas as p','p.id','=','usuarios.id_persona')
            ->join('tipo_documentos_identidades as td','td.id','=','p.id_tipo_documento_identidad')
            ->join('perfiles as pp','pp.id','=','usuarios.id_perfil')
            ->where('usuarios.estado','A')
            ->whereNotIn('usuarios.id_perfil',Variable::isNotModuleUser())
            ->orderBy('usuarios.id','desc');
        return DataTables::of($records)
            ->filterColumn('nombres', function($query, $keyword) {
                $sql = "upper(p.nombres)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('apellido_paterno', function($query, $keyword) {
                $sql = "upper(p.apellido_paterno)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })->filterColumn('apellido_materno', function($query, $keyword) {
                $sql = "upper(p.apellido_materno)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('numero_documento', function($query, $keyword) {
                $sql = "upper(p.numero_documento)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('perfil', function($query, $keyword) {
                $sql = "upper(pp.descripcion)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('tipo_documento', function($query, $keyword) {
                $sql = "upper(td.abreviatura)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->toJson();
    }
}
