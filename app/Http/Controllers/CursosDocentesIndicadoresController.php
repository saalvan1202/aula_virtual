<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\CursoDocenteIndicador;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;


class CursosDocentesIndicadoresController extends Controller
{

  public function store(Request $request)
  {
    set_time_limit(0);
    $uuid=$request->input('uuid');
    $porcentaje=$request->input('porcentaje');
    $request->validate([
      'id'=>'required',
      'codigo'=>'required',
      'porcentaje'=>'required',
      'semana_inicio'=>"required",
      'semana_fin'=>'required',
      'descripcion'=>'required',
      'id_curso_docente'=>'required'
    ],[],[
      'codigo'=>'Codigo',
      'porcentaje'=>'Porcentaje',
      'semana_inicio'=>"Semana Incio",
      'semana_fin'=>'Semana Fin',
      'descripcion'=>'Descripcion',
    ]);
 
    if(CursoDocenteIndicador::validatePorcentajes($uuid,$porcentaje,$request->input('id'))){
      $obj=CursoDocenteIndicador::find($request->input('id'));
      if(is_null($obj)){
        $obj=new CursoDocenteIndicador();
        $obj->estado='A';
      }
      $obj->fill($request->all());
      $obj->save();
      return response()->json($obj);
    }
   return Response()->json([
    'error'=>'La suma de los porcentajes excedió el 100%',
    'data'=>false,
    'status'=>203
    ]) ;
  }
  public function edit($id){
    $obj=CursoDocenteIndicador::findOrFail($id);
    return Response()->json($obj);
  }
  public function destroy($id){
    $obj=CursoDocenteIndicador::findOrFail($id);
    $obj->estado='I';
    $obj->update();
    return Response()->json('ok');
  }
  public function getIndicadores($uuid)
  {
    $indicadores=CursoDocenteIndicador::selectRaw('cursos_docentes_indicadores.*')
    ->join('cursos_docentes as cd','cd.id','=','cursos_docentes_indicadores.id_curso_docente')
    ->where('cursos_docentes_indicadores.estado','A')->where('cd.uuid',$uuid)->orderBy('id','asc')->get();
    return Response()->json($indicadores);
  }
}
