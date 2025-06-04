<?php

namespace App\Http\Controllers;
use App\Models\AsistenciaAlumno;
use App\Models\ClaseDocente;
use App\Models\CursoMatricula;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AsistenciaAlumnoController extends Controller
{
    public function getEstudiantes(Request $request){
        $id_clase_docente=$request->input('id_clase_docente');
        $id_curso_docente=$request->input('id_curso_docente');
        $alumnos=Estudiante::getAsitenciasEstudiantes($id_clase_docente,$id_curso_docente);
        return response()->json($alumnos);
    }
    public function store(Request $request)
    {
        set_time_limit(0);
        return DB::transaction(function () use ($request) {
            foreach ($request->input('detalle') as $value) {
                if($value['id_asistencia']!='-1'){
                    AsistenciaAlumno::where('id',$value['id_asistencia'])
                        ->update([
                            'observaciones'=>trim($value['observaciones']),
                            'tipo_asistencia'=>$value['tipo_asistencia'],
                        ]);
                    continue;
                }
                $obj=new AsistenciaAlumno();
                $obj->id_clase_docente=$request->input('id_clase_docente');
                $obj->id_curso_matricula=$value['id_curso_matricula'];
                $obj->tipo_asistencia=$value['tipo_asistencia'];
                $obj->observaciones=$value['observaciones'];
                $obj->estado='A';
                $obj->save();
            }
            $inh=[];
            $clase=ClaseDocente::find($request->input('id_clase_docente'));
            $clase->estado_clase='DESARROLLADA';
            $clase->update();
            //MANEJAMOS EL 30% DE INACISTENCIA AUTOMÁTICA
            
            $estudiantes=Estudiante::getAsitenciasEstudiantes($request->input('id_clase_docente'),$request->input('id_curso_docente'));
            foreach($estudiantes as $estudiante){
                if($estudiante->porcentaje_faltas>=30){
                    $obj=CursoMatricula::find($estudiante->id_curso_matricula);
                    if(!$obj->estado_nota=='INHABILITADO'){
                        $obj->estado_nota='INHABILITADO';
                        $obj->nota=0;
                        $obj->update();
                    }
                }
            }
            return response()->json('ok');
        });
    }
}
