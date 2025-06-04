<?php

namespace App\Http\Controllers;


use App\Actions\GuardarPersona;
use App\Actions\GuardarUsuario;
use App\Http\Requests\DocenteRequest;
use App\Models\AsistenciaUsuario;
use App\Models\PeriodoClase;
use App\Models\User;
use App\Services\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class AsistenciaUsuarioController
{
    public function indexTeacher()
    {
        return Inertia::render('Teacher/Attendance', [
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'periodo_clases'=>PeriodoClase::anioActual()->where('estado','A')->orderBy('id')->get()
        ]);
    }
    public function listTeacher(Request $request)
    {
        $records=DB::select("select
            u.id as id_usuario,
            p.apellido_paterno||' '||p.apellido_materno||' '||p.nombres as docente,
            tdi.abreviatura as tipo_documento,
            p.numero_documento,
            coalesce(au.id,'-1') as id_asistencia,
            coalesce(au.tipo_asistencia,'') as tipo_asistencia,
            au.observaciones
            from
            usuarios u
            inner join personas p on p.id=u.id_persona
            inner join tipo_documentos_identidades tdi on tdi.id=p.id_tipo_documento_identidad
            left join asistencias_usuarios au on (au.id_usuario=u.id and au.id_periodo_clases=? and au.fecha=?)
            where id_perfil=?",[$request->input('id_periodo_clases'),$request->input('fecha'),Variable::DOCENTE]);
        return Response()->json($records);
    }
    public function store(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'id_periodo_clases' => 'required',
            'fecha' => 'required|date_format:Y-m-d',
        ]);
        return DB::transaction(function () use ($request) {
            foreach ($request->input('detalle') as $value) {
                if($value['id_asistencia']!='-1'){
                    AsistenciaUsuario::where('id',$value['id_asistencia'])
                        ->update([
                            'observaciones'=>trim($value['observaciones']),
                            'tipo_asistencia'=>$value['tipo_asistencia'],
                        ]);
                    continue;
                }
                $obj=new AsistenciaUsuario();
                $obj->id_usuario=$value['id_usuario'];
                $obj->id_periodo_clases=$request->input('id_periodo_clases');
                $obj->fecha=$request->input('fecha');
                $obj->tipo_asistencia=$value['tipo_asistencia'];
                $obj->observaciones=$value['observaciones'];
                $obj->estado='A';
                $obj->save();
            }
            return response()->json('ok');
        });
    }
}
