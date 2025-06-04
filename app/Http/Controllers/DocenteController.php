<?php

namespace App\Http\Controllers;


use App\Actions\GuardarPersona;
use App\Actions\GuardarUsuario;
use App\Http\Requests\DocenteRequest;
use App\Models\User;
use App\Services\Variable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class DocenteController
{
    public function index()
    {

        return Inertia::render('Teacher/Teacher', [
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'tipo_documentos' => DB::table('tipo_documentos_identidades')->where('estado', 'A')->orderBy('id')->get(),
            'tipo_contratos' => DB::table('tipo_contratos')->where('estado', 'A')->orderBy('id')->get(),
        ]);
    }
    public function store(DocenteRequest $request,GuardarPersona $guardarPersona,GuardarUsuario $guardarUsuario)
    {
        set_time_limit(0);
        return DB::transaction(function () use ($request,$guardarPersona,$guardarUsuario) {
            $person=$guardarPersona->handle($request);
            $obj=$guardarUsuario->isTeacher()->handle($person,$request);
            return response()->json($obj);
        });

    }
    public function edit($id)
    {
        $record=User::joinPersona()->where('usuarios.id',$id)->firstOrFail();
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
            ->where('usuarios.id_perfil',Variable::DOCENTE)
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
