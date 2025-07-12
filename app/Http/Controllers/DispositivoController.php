<?php

namespace App\Http\Controllers;

use App\Models\CursoDocente;
use App\Models\CursoDocenteIndicador;
use App\Models\CursoDocenteSeccion;
use App\Models\Dispositivo;
use App\Models\PeriodoClase;
use App\Models\TipoRecurso;
use App\Models\RecursoTarea;
use App\Services\Variable;
use App\Services\RecursoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DispositivoController extends Controller
{

    public function destroy($id)
    {
        // Encuentra el curso_docente_seccion
        $dispositivo = Dispositivo::findOrFail($id);

        // Cambia el estado a 'I'
        $dispositivo->estado = 'I';
        $dispositivo->save();

        return response()->json([
            'message' => 'Dispositivo eliminado correctamente',
            'dispositivo' => $dispositivo
        ]);
    }

}

