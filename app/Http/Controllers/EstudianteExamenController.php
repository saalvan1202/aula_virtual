<?php

namespace App\Http\Controllers;

use App\Traits\ValidatesExam;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EstudianteExamenController extends Controller
{
    use ValidatesExam;

    public function store(Request $request) 
    {
        set_time_limit(0);

        // Validar los inputs
        $validated = $request->validate([
            'id_curso_matricula' => 'required|integer|min:1',
            'id_recurso_examen' => 'required|integer|min:1',
        ]);

        $id_curso_matricula = $validated['id_curso_matricula'];
        $id_recurso_examen = $validated['id_recurso_examen'];

        $validacion_examen = $this->validarExamen($id_recurso_examen);
        if ($validacion_examen instanceof JsonResponse) {
            return $validacion_examen;
        }

        $validacion_examen_enviado = $this->validarExamenEnviado($id_curso_matricula, $id_recurso_examen);
        if ($validacion_examen_enviado instanceof JsonResponse) {
            return $validacion_examen_enviado;
        }

        return response()->json(['message' => 'Examen validado.'], 200);
    }

}