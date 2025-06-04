<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{

    public function store(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'ruc' => 'required|string|size:11',
            'razon_social' => 'required|string|max:300',
            'nombre_comercial' => 'nullable|string|max:150',
            'rubro' => 'nullable|string|max:150',
            'id_ubigeo_empresa' => 'required|integer|min:1',
            'direccion' => 'nullable|string|max:300',
            'telefono' => 'nullable|string|max:50',
            'representante_legal' => 'nullable|string|max:150',
            'email' => 'nullable|string|max:150',
            'pagina_web' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
        ],[],[]);

        $obj = Empresa::find($request->input('id'));
        if (is_null($obj)) {
            $obj = new Empresa();
            $obj->estado = 'A';
        }

        $data = $request->all();
        if (isset($data['id_ubigeo_empresa'])) {
            $data['id_ubigeo'] = $data['id_ubigeo_empresa'];
            unset($data['id_ubigeo_empresa']); // Eliminar el campo original
        }

        $obj->fill($data);
        $obj->save();
        return response()->json([
            'empresa' => $obj->toArray(),
        ]);
    }

    public function autocomplete(Request $request)
    {
        $texto = "%" . preg_replace('/\s+/', '%', strtoupper($request->get('term'))) . "%";
        $records=(new Empresa())->autocomplete($texto);
        return response()->json($records);
    }

}
