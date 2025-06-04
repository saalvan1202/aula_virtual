<?php

namespace App\Http\Controllers;

use App\Actions\GuardarInstituto;
use App\Http\Requests\InstitutoRequest;
use App\Models\Instituto;
use App\Models\Ubigeo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class InstitutoController extends Controller
{
    public function index()
    {
        return Inertia::render('Academy/Institute',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'tipo_gestion_educativas'=>DB::table('tipo_gestion_educativas')->where('estado', 'A')->orderBy('id')->get(),
            'departamentos'=>Ubigeo::selectCombo()->selectDepartamento()->get(),
        ]);
    }
    public function store(InstitutoRequest $request,GuardarInstituto $guardarInstituto)
    {
        $obj=$guardarInstituto->handle($request);
        Cache::forget(schema_name().'_imagenes');
        return response()->json($obj);
    }
    public function edit($id)
    {
        set_time_limit(0);
        $record=Instituto::with('imagenes')->with('ubigeo')->findOrFail($id)->setAppends(['logo','banner']);
        return response()->json($record);
    }
    public function dataTable()
    {
        $records=Instituto::selectRaw('id,denominacion,codigo,direccion,telefono')->where('estado','A')->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }

}
