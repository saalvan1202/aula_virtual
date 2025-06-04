<?php

namespace App\Http\Controllers;

use App\Actions\GuardarProgramaEstudio;
use App\Models\ProgramaEstudio;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class ProgramaEstudioController extends Controller
{
    public function index()
    {
        return Inertia::render('Academy/StudyProgram',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'sedes'=>Sede::select('id','descripcion')->where('estado','A')->get()
        ]);
    }
    public function store(Request $request,GuardarProgramaEstudio $guardarProgramaEstudio)
    {
        $request->validate([
            'id'=>'required',
            'descripcion'=>'required:max:100',
            'nivel_formativo'=>'nullable:max:100',
            'actividad_economica'=>'nullable:max:100',
            'duracion'=>'nullable:max:100',
            'sedes'=>'required|array',
        ],[],[
            'descripcion'=>'Nombre',
        ]);
        return DB::transaction(function () use ($request,$guardarProgramaEstudio){
            $obj=$guardarProgramaEstudio->handle($request);
            return response()->json($obj);
        });

    }
    public function edit($id)
    {
        $record=ProgramaEstudio::withSedes()->findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=ProgramaEstudio::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function dataTable()
    {
        set_time_limit(0);
        $sql="select
            pe.id,
            pe.codigo,
            pe.descripcion,
            pe.nivel_formativo,
            array_to_string(array_agg(s.descripcion order by s.id), ', ') as sedes
            from
            programa_estudios pe
            left join programas_sedes ps on (ps.id_programa_estudio=pe.id and ps.estado='A')
            left join sedes s on s.id=ps.id_sede
            where pe.estado='A'
            group by pe.id";
        $records=DB::table(DB::raw("($sql) as tbl"));
        return DataTables::of($records)->toJson();
    }
    public function getProgramaEstudioBySede($id_sede)
    {
        $records = DB::table('programa_estudios as pe')
            ->join('programas_sedes as ps', 'pe.id', '=', 'ps.id_programa_estudio')
            ->where('ps.id_sede', $id_sede)
            ->where('ps.estado', 'A')
            ->select('pe.*')
            ->get();

        return response()->json($records);
    }
}
