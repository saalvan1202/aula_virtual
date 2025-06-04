<?php

namespace App\Http\Controllers;

use App\Models\CursoDocente;
use App\Models\CursoDocenteIndicador;
use App\Models\CursoDocenteSeccion;
use App\Models\PeriodoClase;
use App\Models\TipoRecurso;
use App\Models\RecursoTarea;
use App\Services\Variable;
use App\Services\RecursoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AulaVirtualController extends Controller
{
    public function index($uuid)
    {
        $perfil=auth()->user()->id_perfil;

        $curso = [];
        if(Variable::isTeacher($perfil)){
            $curso = CursoDocente::getInfoCurso($uuid);
            $curso_docente_seccion = CursoDocenteSeccion::where('estado', 'A')
            ->with(['seccionRecurso' => function ($query) {
                $query->where('estado', 'A')
                    ->orderBy('created_at', 'asc')
                    ->withTipoRecurso()
                    ->withRecursoExamen()
                    ->withRecursoForo()
                    ->withRecursoTarea();
            }])
            ->where('id_curso_docente', $curso->id)
            ->orderBy('created_at', 'asc') // Ordenar por fecha de creación en orden ascendente
            ->get();

        } elseif (Variable::isStudent($perfil)) {
            $curso = CursoDocente::getInfoCursoEstudiante($uuid);
            $id_curso_matricula = $curso->id_curso_matricula;

            $curso_docente_seccion_procesado = CursoDocenteSeccion::where('estado', 'A')
                ->with(['seccionRecurso' => function ($query) use ($id_curso_matricula) {
                    $query->where('estado', 'A')
                        ->orderBy('created_at', 'asc')
                        ->withTipoRecurso()
                        ->withRecursoExamen($id_curso_matricula)
                        ->withRecursoForo($id_curso_matricula)
                        ->withRecursoTarea($id_curso_matricula);
                }])
                ->where('id_curso_docente', $curso->id)
                ->orderBy('created_at', 'asc') // Ordenar por fecha de creación en orden ascendente
                ->get();

            /*$recursoService = new RecursoService();
            // Código principal
            $curso_docente_seccion_procesado = $curso_docente_seccion_procesado->map(function ($seccion) use ($id_curso_matricula, $recursoService) {
                $seccion->seccionRecurso->map(function ($recurso) use ($id_curso_matricula, $recursoService) {
                    $fechaActual = Carbon::now();

                    // Procesar recursoTarea, recursoForo y recursoExamen
                    $recursoService->procesarRecursos($recurso->recursoTarea, $fechaActual);
                    $recursoService->procesarRecursos($recurso->recursoForo, $fechaActual);
                    $recursoService->procesarRecursos($recurso->recursoExamen, $fechaActual);
                });
                return $seccion;
            });*/

        }

        $tipo_recurso = TipoRecurso::where('estado', 'A')->get();

        $tipo_archivo = RecursoTarea::getTipoArchivos();

        $curso_docente_import=CursoDocente::select('cursos_docentes.id_periodo_clases as id','pc.anio')
            ->join('periodo_clases as pc','pc.id','=','cursos_docentes.id_periodo_clases')
            ->where('cursos_docentes.estado','A')
            ->where('pc.estado','A')
            ->where('uuid',$uuid)->first();

        $periodo=PeriodoClase::where('id',"!=",$curso_docente_import->id)->where('anio','<=',$curso_docente_import->anio)
            ->where('estado','A')->orderBy('anio','desc')->orderBy('id','desc')->get();

        $componente='';
        $props=[
            'urlRoute' => ltrim(request()->route()->getPrefix(), '/'),
            'curso' => $curso,
            'secciones' => [],
            'tipos_recursos' => $tipo_recurso,
            'tipos_archivos' => $tipo_archivo,
            'periodo'=>$periodo,
            'uuid'=>$uuid,
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ];

        if(Variable::isTeacher($perfil)){
            $componente='VirtualClassroom/Classroom';
            $props['secciones'] = $curso_docente_seccion;
            $props['semanas']=CursoDocenteIndicador::getSemanas();
        } elseif (Variable::isStudent($perfil)) {
            $componente='VirtualClassroom/Student/Classroom';
            $props['secciones'] = $curso_docente_seccion_procesado;
        }
        return Inertia::render($componente, $props);
    }

    public function store(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'id_curso_docente' => 'required|integer|min:1',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:250',
        ], [], []);

        $curso_docente_seccion = new CursoDocenteSeccion();
        $curso_docente_seccion -> id_curso_docente = $request -> input('id_curso_docente');
        $curso_docente_seccion -> nombre = $request -> input('nombre');
        $curso_docente_seccion -> descripcion = $request -> input('descripcion');
        $curso_docente_seccion -> estado = 'A';
        $curso_docente_seccion -> save();
        return response()->json($curso_docente_seccion);
    }

    public function update(Request $request, $id)
    {
        set_time_limit(0);

        $validatedData = $request->validate([
            'nombre' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:250',
        ]);

        // Encuentra el curso_docente_seccion
        $cursoDocenteSeccion = CursoDocenteSeccion::findOrFail($id);

        // Actualiza los valores
        $cursoDocenteSeccion->update([
            'nombre' => $validatedData['nombre'] ?? $cursoDocenteSeccion->nombre,
            'descripcion' => $validatedData['descripcion'] ?? $cursoDocenteSeccion->descripcion,
        ]);

        return response()->json([
            'message' => 'Datos actualizados correctamente',
            'curso_docente_seccion' => $cursoDocenteSeccion
        ]);
    }

    public function destroy($id)
    {
        // Encuentra el curso_docente_seccion
        $cursoDocenteSeccion = CursoDocenteSeccion::with('seccionRecurso')->findOrFail($id);

        // Cambia el estado a 'I'
        $cursoDocenteSeccion->estado = 'I';
        $cursoDocenteSeccion->save();

        // Cambia el estado de todas las seccion_recurso relacionadas
        $cursoDocenteSeccion->seccionRecurso()->update(['estado' => 'I']);

        return response()->json([
            'message' => 'Estado actualizado correctamente',
            'curso_docente_seccion' => $cursoDocenteSeccion
        ]);
    }

    public function getSeccionCursosDocente($id_curso,$id_periodo_clases)
    {
        $secciones=CursoDocenteSeccion::selectRaw('cursos_docentes_secciones.nombre,cursos_docentes_secciones.id,
            cursos_docentes_secciones.id_curso_docente')
            ->join('cursos_docentes as cd','cd.id','=','cursos_docentes_secciones.id_curso_docente')
            ->where('cd.estado','A')
            ->where('cursos_docentes_secciones.estado','A')
            ->where('cd.id_periodo_clases',$id_periodo_clases)
            ->where('cd.id_docente',auth()->user()->id)
            ->where('cd.id_cursos',$id_curso)
            ->get();
       return response()->json($secciones);
    }

}

