<?php

namespace App\Http\Controllers;

use App\Models\Ambiente;
use App\Models\Ciclo;
use App\Models\PeriodoClase;
use App\Models\Seccion;
use App\Models\Sede;
use App\Models\TipoAmbiente;
use App\Models\Turno;
use App\Models\User;
use App\Models\CursoDocente;
use App\Models\Horario;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HorarioController extends Controller
{
    public function index()
    {
        return Inertia::render('Schedule/Schedule',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'sedes'=>Sede::where('estado','A')->get(),
            'tipo_ambiente'=>TipoAmbiente::where('estado','A')->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Schedule/AddSchedule',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'horariosEditMode' => false,
            'horarios_to_edit'=>[],
            'sedes'=>Sede::where('estado','A')->get(),
            'tipo_ambientes'=>TipoAmbiente::where('estado','A')->get(),
            'periodo_clases'=>PeriodoClase::where('estado','A')->get(),
            'secciones'=>Seccion::where('estado','A')->get(),
            'turnos'=>Turno::where('estado','A')->get(),
            'docentes' => User::where('estado', 'A')
                ->where('id_perfil', 4)
                ->with('persona')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'persona' => $user->persona,
                    ];
                }),
            'ciclos'=> Ciclo::where('estado','A')->orderby('orden','asc')->get(),
            'menu_dependencia'=>[
                [
                    'menu'=>'horarios',
                    'url'=>ltrim(request()->getPathInfo(),'/')
                ]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'id_sede'=>'required|integer',
            'descripcion'=>'required|max:100',
            'id_tipo_aula'=>'required|integer',
            'piso'=>'required|integer',
            'ubicacion'=>'required|max:100',
            'aforo'=>'required|integer',
        ],[],[
            'id_sede'=>'sede',
            'id_tipo_aula'=>'tipo Aula'
        ]);
        $obj=Ambiente::find($request->input('id'));
        if(is_null($obj)){
            $obj=new Ambiente();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }

    public function edit($id)
    {
        $horarios = Horario::where('id_curso_docente', $id)
            ->where('estado', 'A')
            ->with(['ambiente.tipoAmbiente'])
            ->get()
            ->map(function ($horario) {
                return [
                    'id_horario' => $horario->id,
                    'aforo' => $horario->ambiente->aforo,
                    'dia' => $horario->dia,
                    'tipo' => $horario->tipo,
                    'hora_inicio' => $horario->hora_inicio,
                    'hora_fin' => $horario->hora_fin,
                    'aula' => $horario->ambiente->descripcion,
                    'id_aula' => $horario->id_aula,
                    'id_tipo_aula' => $horario->ambiente->id_tipo_aula,
                    'tipo_aula' => $horario->ambiente->tipoAmbiente->descripcion,
                ];
            });

        return Inertia::render('Schedule/AddSchedule', [
            'urlRoute' => ltrim(request()->route()->getPrefix(), '/'),
            'horarios_to_edit' => $horarios,
            'tipo_ambientes'=>TipoAmbiente::where('estado','A')->get(),
            'curso_docente_to_edit' => intval($id),
            'horarios_edit_mode' => true,
            'menu_dependencia'=>[
                [
                    'menu'=>'horarios',
                    'url'=>ltrim(request()->getPathInfo(),'/')
                ]
            ]
        ]);
    }

    public function patchHorario(Request $request, $id)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'aforo' => 'nullable|integer',
            'dia' => 'nullable|string|max:15',
            'tipo' => 'nullable|string|max:50',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
            'id_aula' => 'nullable|integer',
            'estado' => 'nullable|string|max:1',
        ]);

        // Buscar el horario
        $horario = Horario::findOrFail($id);

        // Actualizar los campos
        $horario->update([
            'id_aula' => $validatedData['id_aula'] ?? $horario->id_aula,
            'dia' => $validatedData['dia'] ?? $horario->dia,
            'tipo' => $validatedData['tipo'] ?? $horario->tipo,
            'hora_inicio' => $validatedData['hora_inicio'] ?? $horario->hora_inicio,
            'hora_fin' => $validatedData['hora_fin'] ?? $horario->hora_fin,
            'estado' => $validatedData['estado'] ?? $horario->estado,
        ]);

        // Retornar la respuesta
        return response()->json([
            'message' => 'Horario actualizado con éxito.',
            'horario' => $horario,
        ]);
    }

    public function storeHorario(Request $request)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'aforo' => 'required|integer',
            'dia' => 'required|string|max:15',
            'tipo' => 'required|string|max:15',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'id_aula' => 'required|integer',
            'id_curso_docente' => 'required|integer',
        ]);

        $horario = Horario::create([
            'aforo' => $validatedData['aforo'],
            'dia' => $validatedData['dia'],
            'tipo' => $validatedData['tipo'],
            'hora_inicio' => $validatedData['hora_inicio'],
            'hora_fin' => $validatedData['hora_fin'],
            'id_aula' => $validatedData['id_aula'],
            'id_curso_docente' => $validatedData['id_curso_docente'],
            'estado' => 'A'
        ]);

        // Retornar la respuesta
        return response()->json([
            'message' => 'Horario creado con éxito.',
            'horario' => $horario,
        ]);
    }

    public function destroy($id)
    {
        $obj=CursoDocente::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        Horario::where('id_curso_docente', $obj->id)
            ->update([
                'estado'=>'I',
            ]);
        return response()->json('ok');
    }

    public function dataTable()
    {
        set_time_limit(0);
        $sql="select
                cd.id,
                pc.descripcion as periodo,
                c.descripcion as curso,
                t.descripcion as turno,
                e.descripcion as programa,
                s.descripcion as seccion,
                p.apellido_paterno|| ' '||p.apellido_materno||' '||p.nombres as docente,
                 array_to_string(array_agg(h.dia||' ['||h.hora_inicio||' - '||h.hora_fin || ' (' || a.descripcion || ')' || ']' order by h.dia,h.hora_fin,h.hora_fin), ' - ') as horario
                FROM cursos_docentes cd
                inner join usuarios u on u.id=cd.id_docente
                inner join personas p on p.id=u.id_persona
                inner join periodo_clases pc on pc.id=cd.id_periodo_clases
                inner join cursos c on c.id = cd.id_cursos
                inner join horarios h on h.id_curso_docente=cd.id
                inner join secciones s on cd.id_seccion=s.id
                inner join aulas a on a.id=h.id_aula
                inner join turnos t on t.id = cd.id_turno
                inner join plan_estudios pe on pe.id = c.id_plan_estudio
                inner join programa_estudios e on e.id = pe.id_programa_estudio
                where cd.estado = 'A' and h.estado = 'A'
                group by cd.id,pc.descripcion,c.descripcion,docente,turno,programa,seccion";
        $records=DB::table(DB::raw("($sql) as tbl"));
        return DataTables::of($records)->toJson();
    }

    public function storeCursoDocenteAndHorarios(Request $request)
    {
        $validatedData = $request->validate([
            'cursos_docentes.id_periodo_clases' => 'required|integer',
            'cursos_docentes.id_sede' => 'required|integer',
            'cursos_docentes.id_turno' => 'required|integer',
            'cursos_docentes.id_seccion' => 'required|integer',
            'cursos_docentes.id_docente' => 'required|integer',
            'cursos_docentes.id_cursos' => 'required|integer',
            'cursos_docentes.estado' => 'required|string|max:1',
            'horarios.*.id_aula' => 'required|integer',
            'horarios.*.dia' => 'required|string',
            'horarios.*.tipo' => 'required|string',
            'horarios.*.hora_inicio' => 'required|string',
            'horarios.*.hora_fin' => 'required|string',
            'horarios.*.estado' => 'required|string|max:1',
        ]);

        DB::beginTransaction();

        try {
            // Generar UUID
            $uuid = (string) Str::uuid();

            // Crear CursoDocente con UUID
            $cursoDocente = CursoDocente::create([
                'id_periodo_clases' => $validatedData['cursos_docentes']['id_periodo_clases'],
                'id_sede' => $validatedData['cursos_docentes']['id_sede'],
                'id_turno' => $validatedData['cursos_docentes']['id_turno'],
                'id_seccion' => $validatedData['cursos_docentes']['id_seccion'],
                'id_docente' => $validatedData['cursos_docentes']['id_docente'],
                'id_cursos' => $validatedData['cursos_docentes']['id_cursos'],
                'estado' => $validatedData['cursos_docentes']['estado'],
                'uuid' => $uuid, // Incluye el uuid generado
            ]);

            // Crear Horarios
            foreach ($validatedData['horarios'] as $horario) {
                Horario::create([
                    'id_curso_docente' => $cursoDocente->id,
                    'id_aula' => $horario['id_aula'],
                    'dia' => $horario['dia'],
                    'tipo' => $horario['tipo'],
                    'hora_inicio' => $horario['hora_inicio'],
                    'hora_fin' => $horario['hora_fin'],
                    'estado' => $horario['estado'],
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Datos guardados correctamente', 'uuid' => $uuid], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al guardar los datos', 'error' => $e->getMessage()], 500);
        }
    }

    public function verifyCursoDocente(Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'id_docente' => 'required|integer',
            'id_cursos' => 'required|integer',
            'id_periodo_clases' => 'required|integer',
            'id_seccion' => 'required|integer',
        ]);

        // Verificar si existe un registro con los id_docente e id_cursos proporcionados
        $exists = CursoDocente::where('id_docente', $validatedData['id_docente'])
            ->where('id_cursos', $validatedData['id_cursos'])
            ->where('id_periodo_clases', $validatedData['id_periodo_clases'])
            ->where('id_seccion', $validatedData['id_seccion'])
            ->where('estado', 'A')
            ->exists();

        return response()->json(['exists' => $exists], 200);
    }

    public function checkHorarioConflict(Request $request)
    {
        $validatedData = $request->validate([
            'dia' => 'required|string',
            'id_aula' => 'required|integer',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        try {
            $conflictingHorario = Horario::where('dia', $validatedData['dia'])
                ->where('id_aula', $validatedData['id_aula'])
                ->where(function ($query) use ($validatedData) {
                    $query->where('hora_inicio', '<', $validatedData['hora_fin'])
                          ->where('hora_fin', '>', $validatedData['hora_inicio']);
                })
                ->where('estado', 'A')
                ->with('cursoDocente.curso') // Asegúrate de que la relación 'curso' esté definida en el modelo CursoDocente
                ->first();

            if ($conflictingHorario) {
                $cursoDocente = $conflictingHorario->cursoDocente;
                $curso = $cursoDocente->curso; // Asegúrate de que la relación 'curso' esté definida en el modelo CursoDocente
                $docente = $cursoDocente->docente; // Asegúrate de que la relación 'docente' esté definida en el modelo CursoDocente
                $persona = $docente->persona; // Asegúrate de que la relación 'persona' esté definida en el modelo User

                return response()->json([
                    'conflict' => true,
                    'conflicting_range' => [
                        'hora_inicio' => $conflictingHorario->hora_inicio,
                        'hora_fin' => $conflictingHorario->hora_fin,
                        'dia' => $conflictingHorario->dia,
                        'curso' => $curso->descripcion, // Ajusta esto según el nombre del campo en tu modelo Curso
                        'nombres' => $persona->nombres,
                        'apellido_paterno' => $persona->apellido_paterno,
                        'apellido_materno' => $persona->apellido_materno,
                        'telefono' => $docente->telefono,
                    ],
                ], 200);
            }

            return response()->json(['conflict' => false], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al verificar el conflicto de horario', 'error' => $e->getMessage()], 500);
        }
    }

}

