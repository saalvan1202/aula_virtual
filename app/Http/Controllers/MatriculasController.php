<?php

namespace App\Http\Controllers;

use App\Actions\GuardarMatricula;
use App\Http\Requests\MatriculaRequest;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Instituto;
use App\Models\Matricula;
use App\Models\Matriculas;
use App\Models\PeriodoClase;
use App\Models\Seccion;
use App\Models\Sede;
use App\Models\TipoMatricula;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

use function PHPUnit\Framework\isNull;

class MatriculasController extends Controller
{
    public function index()
    {
        return Inertia::render('Matricula/RegistrarMatricula',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'tipo_proceso'=>DB::table('tipo_procesos_matriculas')->where('estado', 'A')->orderBy('id')->get(),
            'tipo_matricula'=>TipoMatricula::where('estado', 'A')->orderBy('id')->get(),
            'turnos'=>Turno::where('estado', 'A')->orderBy('id')->get(),
            'secciones'=>Seccion::where('estado', 'A')->orderBy('id')->get(),
            'periodo_clases'=>PeriodoClase::where('estado','A')->orderBy('id')->get(),
            'sedes'=>DB::table('sedes')->where('estado', 'A')->orderBy('id')->get(),
        ]);
    }
    public function autocomplete(Request $request)
    {
        $texto = "%" . preg_replace('/\s+/', '%', strtoupper($request->get('term'))) . "%";
        $records=(new Estudiante())->autocomplete($texto);
        return response()->json($records);
    }
    public function searchCursos(Request $request){
        $estadoMatricula=Matricula::where('id_estudiante',$request->id)->where('id_periodo_clases',$request->id_periodo_clases)
        ->where('estado','A')->first();
            $id_plan_estudio=$request->id_plan_estudio;
            $id_ciclo=$request->id_ciclo;
            $id_peridoso_clases=$request->id_periodo_clases;
            $id_seccion=$request->id_seccion;
            $id_turno=$request->id_turno;
            $id_sede=$request->input('id_sede');
                $sql="
                select
                cd.id,
                c.descripcion as curso,
                s.descripcion as seccion,
                c.creditos_teoria,
                c.creditos_practica,
                cd.id_seccion
                from
                cursos_docentes as cd
                inner join cursos as c on cd.id_cursos=c.id
                inner join secciones as s on cd.id_seccion=s.id
                where c.estado='A'
                and c.id_plan_estudio=?
                and c.id_ciclo=?
                and cd.id_turno=?
                and cd.id_periodo_clases=?
                and cd.id_seccion=?
                and cd.id_sede=?
                order by c.id_ciclo
                limit 10";
            return DB::select($sql,[$id_plan_estudio,$id_ciclo,$id_turno,$id_peridoso_clases,$id_seccion,$id_sede]);
    }
    public function store(GuardarMatricula $guardarMatricula,MatriculaRequest $request)
    {
    return DB::transaction(function()use($guardarMatricula,$request){
        $matricula=$guardarMatricula->handle($request);
        $ids = $request->input('id_cursos_docente');
        foreach ($ids as $id) {
            $existingRecord = DB::table('cursos_matriculas')->where('id_matricula', $matricula->id)->where('id_curso_docente',$id)->first();
            if (!$existingRecord) {
                DB::table('cursos_matriculas')->insert([
                    'id_matricula' => $matricula->id,
                    'id_curso_docente' => $id,
                    'estado'=>'A',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        return response()->json(['success' => 'Matrículas guardadas correctamente']);
    });
    }
    public function edit($id)
    {
        $record=Matricula::withEstudiante()->withCursosMatricula()->findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=Matricula::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    // public function dataTable($id_sede)
    // {
    //     $records=Matricula::selectRaw("matriculas.id,p.numero_documento as numero_documento,p.nombres||' '||p.apellido_paterno||' '||p.apellido_materno as estudiante,
    //     tm.descripcion as tipo_matricula,t.descripcion as turno,pc.descripcion as semestre,c.descripcion as ciclo,pe.descripcion as programa_estudio,
    //     pl.descripcion as plan_estudio")
    //         ->join('estudiantes as e','e.id','=','matriculas.id_estudiante')
    //         ->join('sedes as s','s.id','=','e.id_sede')
    //         ->join('usuarios as u','u.id','=','e.id_usuario')
    //         ->join('personas as p','p.id','=','u.id_persona')
    //         ->join('tipo_documentos_identidades as td','td.id','=','p.id_tipo_documento_identidad')
    //         ->join('tipo_matriculas as tm','tm.id','=','matriculas.id_tipo_matricula')
    //         ->join('turnos as t','t.id','=','matriculas.id_turno')
    //         ->join('periodo_clases as pc','pc.id','=','matriculas.id_periodo_clases')
    //         ->join('ciclos as c','c.id','=','matriculas.id_ciclo')
    //         ->join('programa_estudios as pe','pe.id','=','e.id_programa_estudio')
    //         ->join('plan_estudios as pl','pl.id','=','e.id_plan_estudio')
    //         ->where('matriculas.estado','A')->orderBy('matriculas.id','desc');
    //     return DataTables::of($records)->toJson();
    // }
    public function dataTable($id_sede)
    {
        $records = Matricula::selectRaw("
                matriculas.id,
                p.numero_documento as numero_documento,
                p.nombres || ' ' || p.apellido_paterno || ' ' || p.apellido_materno as estudiante,
                tm.descripcion as tipo_matricula,
                t.descripcion as turno,
                pc.descripcion as semestre,
                c.descripcion as ciclo,
                pe.descripcion as programa_estudio,
                pl.descripcion as plan_estudio
            ")
            ->join('estudiantes as e', 'e.id', '=', 'matriculas.id_estudiante')
            ->join('sedes as s', 's.id', '=', 'e.id_sede')
            ->join('usuarios as u', 'u.id', '=', 'e.id_usuario')
            ->join('personas as p', 'p.id', '=', 'u.id_persona')
            ->join('tipo_documentos_identidades as td', 'td.id', '=', 'p.id_tipo_documento_identidad')
            ->join('tipo_matriculas as tm', 'tm.id', '=', 'matriculas.id_tipo_matricula')
            ->join('turnos as t', 't.id', '=', 'matriculas.id_turno')
            ->join('periodo_clases as pc', 'pc.id', '=', 'matriculas.id_periodo_clases')
            ->join('ciclos as c', 'c.id', '=', 'matriculas.id_ciclo')
            ->join('programa_estudios as pe', 'pe.id', '=', 'e.id_programa_estudio')
            ->join('plan_estudios as pl', 'pl.id', '=', 'e.id_plan_estudio')
            ->where('matriculas.estado', 'A')
            ->orderBy('matriculas.id', 'desc');

        // Filtro condicional por sede
        if ($id_sede != 0) {
            $records->where('s.id', $id_sede);
        }

        return DataTables::of($records)->toJson();
    }

}
