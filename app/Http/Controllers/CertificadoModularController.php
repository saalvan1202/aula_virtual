<?php

namespace App\Http\Controllers;

use App\Actions\Pdfs\CertificadoModularPdf;
use App\Http\Requests\CertificadoModularRequest;
use App\Models\Certificado;
use App\Models\Instituto;
use App\Models\ModuloCompetencia;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class CertificadoModularController
{
    public function index()
    {
        return Inertia::render('Certificate/Modulate',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
        ]);
    }
    public function module(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'id_estudiante' => 'required|integer|required_zero',
            'id_plan_estudio' => 'required|integer|required_zero',
        ]);
        $records=(new ModuloCompetencia())->getModulosParaCertificado($request->input('id_estudiante'),$request->input('id_plan_estudio'));
        return response()->json($records);
    }
    public function store(CertificadoModularRequest $request)
    {
        set_time_limit(0);
        $instituto=Instituto::where('estado','A')->first();
        $obj=Certificado::find($request->input('id'));
        if(is_null($obj)){
            $obj=new Certificado();
            $obj->tipo='MODULAR';
            $obj->fecha=date('Y-m-d');
            $obj->estado='A';
        }
        if($obj->tipo!='MODULAR'){
            throw ValidationException::withMessages([
                'id_estudiante' => ['este certificado no es modular no puedes modificar por este modulo'],
            ]);
        }
        $obj->director_general=$instituto->director_general;
        $obj->id_plan_estudio=$request->input('id_plan_estudio');
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function edit($id)
    {
        $cert=Certificado::withEstudiante()->findOrFail($id);
        return response()->json($cert);
    }
    public function destroy($id)
    {

    }
    public function print($id,CertificadoModularPdf $pdf)
    {
        set_time_limit(0);
        $cert=Certificado::withEstudiante()->withModulo()->withPlanEstudio()->find($id);
        if($cert->tipo!='MODULAR'){
            die('NO ES UN CERTIFICADO MODULAR');
        }
        $instituto=Instituto::with('tipoGestionEducativa')->where('estado','A')->first();
        $pdf->print($cert,$instituto);

    }
    public function dataTable()
    {
        $records=Certificado::selectRaw("
            certificados.id,
            certificados.fecha,
            p.apellido_paterno ||' '||p.apellido_materno ||' '||p.nombres as estudiante,
            mc.descripcion as modulo
        ")
            ->join('estudiantes as e','e.id','=','certificados.id_estudiante')
            ->join('usuarios as u','u.id','=','e.id_usuario')
            ->join('personas as p','p.id','=','u.id_persona')
            ->join('modulos_competencias as mc','mc.id','=','certificados.id_referencia')
            ->where('certificados.estado','A')
            ->where('tipo','MODULAR')->orderBy('certificados.id','desc');
        return DataTables::of($records)
            ->filterColumn('estudiante', function($query, $keyword) {
                $sql="trim(p.apellido_paterno ||' '||p.apellido_materno ||' '||p.nombres) ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('modulo', function($query, $keyword) {
                $sql = "upper(mc.descripcion)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->toJson();
    }
}
