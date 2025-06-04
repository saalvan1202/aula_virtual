<?php

namespace App\Http\Controllers;

use App\Models\Ubigeo;
use Illuminate\Http\Request;

class UbigeoController
{
    public function index()
    {

    }
    public function department()
    {
        set_time_limit(0);
        $records=Ubigeo::selectCombo()->selectDepartamento()->get();
        return response()->json($records);
    }
    public function province(Request $request)
    {
        set_time_limit(0);
        $records=Ubigeo::selectCombo()->selectProvincia($request->input('cod_dpto'))->get();
        return response()->json($records);
    }
    public function district(Request $request)
    {
        set_time_limit(0);
        $records=Ubigeo::selectCombo()->selectDistrito($request->input('cod_dpto'),$request->input('cod_prov'))->get();
        return response()->json($records);
    }
}
