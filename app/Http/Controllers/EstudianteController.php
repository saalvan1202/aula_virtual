<?php

namespace App\Http\Controllers;

use App\Actions\CrearArchivo;
use App\Actions\GuardarPersona;
use App\Actions\GuardarUsuario;
use App\Models\Ubigeo;
use App\Models\Estudiante;
use App\Services\Variable;
use App\Services\EstudianteService;
use App\Http\Requests\EstudianteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;
use Yajra\DataTables\DataTables;

class EstudianteController
{
    public function index()
    {

        return Inertia::render('Matricula/Student', [
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'paises'=>DB::table('paises')->where('estado', 'A')->orderBy('id')->get(),
            'tipo_documentos' => DB::table('tipo_documentos_identidades')->where('estado', 'A')->orderBy('id')->get(),
            'tipo_contratos' => DB::table('tipo_contratos')->where('estado', 'A')->orderBy('id')->get(),
            'modalidades_educativas'=>DB::table('modalidades_educativas')->where('estado', 'A')->orderBy('id')->get(),
            'niveles_educativos'=>DB::table('nivel_educativas')->where('estado', 'A')->orderBy('id')->get(),
            'tipo_gestion_educativas'=>DB::table('tipo_gestion_educativas')->where('estado', 'A')->orderBy('id')->get(),
            'lenguas_maternas'=>DB::table('lenguas_maternas')->where('estado', 'A')->orderBy('id')->get(),
            'departamentos'=>Ubigeo::selectCombo()->selectDepartamento()->get(),
            'sedes'=>DB::table('sedes')->where('estado', 'A')->orderBy('id')->get(),
            'tipo_estudiantes'=>DB::table('tipo_estudiantes')->where('estado', 'A')->orderBy('id')->get(),
            'ciclos'=>DB::table('ciclos')->where('estado', 'A')->orderBy('id')->get(),
            'periodo_clases'=>DB::table('periodo_clases')->where('estado', 'A')->orderBy('id')->get(),
            'id_peru'=>Variable::IDPERU
        ]);
    }

    public function getDistritos($id)
    {
        //Para ejecutar el get ->get("")
        $distrito=DB::table('ubigeos')->where('estado', 'A')->where('id',$id)->orderBy('id')->first();
        return response()->json($distrito);
    }

    //LLAMRA O FILTRAR LOS PROGRAMAS DE ESTUDIO
    public function getProgramaSedes($id)
    {
        $idProgramas=DB::table('programas_sedes')->where('estado', 'A')->where('id_sede',$id)->orderBy('id')->pluck('id_programa_estudio');
        $programas=DB::table('programa_estudios')->where('estado', 'A')->whereIn('id',$idProgramas)->get();
        
        return response()->json($programas);
    }

    public function getPlanProgramas($id)
    {
        $planes=DB::table('plan_estudios')->where('estado', 'A')->where('id_programa_estudio',$id)->orderBy('id')->get();
        return response()->json($planes);
    }

    public function store(EstudianteRequest $request,GuardarPersona $guardarPersona,GuardarUsuario $guardarUsuario,CrearArchivo $crearArchivo)
    {
        set_time_limit(0);
        return DB::transaction(function () use ($request,$guardarPersona,$guardarUsuario,$crearArchivo) {
            $person=$guardarPersona->handle($request);
            $user=$guardarUsuario->setKeyId('id_usuario')->isStudent()->handle($person,$request);
            $obj=Estudiante::find($request->input('id'));
            if(is_null($obj)){
                $obj=new Estudiante();
                $obj->id_usuario=$user->id;
                $obj->estado='A';
                $obj->discapacidad=$obj->discapacidad?'S':"N";
                $obj->menor_edad=$obj->menor_edad?"S":"N";
                $obj->id_periodo_ingreso=0;
            }
            $obj->fill($request->all());
            $obj->discapacidad=$obj->discapacidad?'S':"N";
            $obj->menor_edad=$obj->menor_edad?"S":"N";
            $obj->save();
            if($obj->menor_edad=='S'){
                $ids=[];
                if($request->filled('apoderados')){
                    foreach($request->input('apoderados') as $key=>$value){
                        $repre='N';
                        if($key==0){
                            $repre='S';
                        }
                        $found=$obj->detalleApoderado()->where('id_apoderado',$value['id_apoderado'])->first();
                        if($found){
                            $found->estado='A';
                            $found->parentesco=$value['parentesco'];
                            $found->representante=$repre;
                            $found->update();
                            $ids[] = $value['id_apoderado'];
                            continue;
                        }
                        $obj->detalleApoderado()->create([
                            'id_apoderado'=>$value['id_apoderado'],
                            'parentesco'=>$value['parentesco'],
                            'representante'=>$repre,
                            'estado'=>'A'
                        ]);
                        $ids[]=$value['id_apoderado'];
                    }
                }
                if(count($ids)==0){
                    $ids[]='-1';
                }
                $obj->detalleApoderado()->whereNotIn('id_apoderado',$ids)->update([
                    'estado'=>'I'
                ]);
            }
            if($request->hasFile('foto')){
                $crearArchivo->setSubject($obj)->handle($request->file('foto'));
            }


            return response()->json($obj);
        });

    }

    public function edit($id)
    {
        $record=Estudiante::joinPersona()->withApoderado()->with('foto')->with('distrito')->with('distritoI')->where('estudiantes.id',$id)->firstOrFail();
        if($record->foto){
            $record->foto->setAppends(['base64']);
        }
        $record->password_vista=Variable::PASSWORD;
        return response()->json($record);
    }

    public function destroy($id)
    {
        $obj=Estudiante::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }

    public function import(Request $request, GuardarPersona $guardarPersona, GuardarUsuario $guardarUsuario, EstudianteService $estudiante_service)
    {
        $validatedData = $request->validate([
            'id_sede' => 'required|integer|min:0',
            'excel_file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        $reader = null;
        DB::beginTransaction();

        try {
            $file = $request->file('excel_file');
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->getPathname());

            $columnMap = [
                'DOCUMENTO' => 'numero_documento',
                'NOMBRES' => 'nombres',
                'APELLIDO PATERNO' => 'apellido_paterno',
                'APELLIDO MATERNO' => 'apellido_materno',
                'SEXO' => 'sexo',
                'FECHA NACIMIENTO' => 'fecha_nacimiento',
                'CORREO' => 'email',
                'CELULAR' => 'telefono',
                'PROGRAMA DE ESTUDIOS' => 'programa_estudios',
                'CICLO' => 'ciclo',
                'TIPO INSTITUCIÓN IE' => 'modalidad_educativa',
                'TIPO GESTIÓN IE' => 'tipo_gestion',
                'CÓDIGO MODULAR IE' => 'codigo_modular',
                'NOMBRE IE' => 'nombre_ie',
                'UBIGEO IE' => 'ubigeo_ie',
                'AÑO DE EGRESO' => 'anio_egreso',
                'ESTADO MATRICULA' => 'estado_matricula'
            ];

            $result = $estudiante_service->processImportFile(
                $reader,
                $validatedData['id_sede'],
                $columnMap,
                $guardarPersona,
                $guardarUsuario
            );

            DB::commit();

            return response()->json([
                'success' => $result['importedCount'] > 0,
                'message' => $result['importedCount'] > 0 
                    ? "{$result['importedCount']} estudiantes importados correctamente" 
                    : "No se importaron estudiantes",
                'total_rows' => $result['totalRows'],
                'warnings' => $result['errors']
            ], $result['importedCount'] > 0 ? 200 : 400);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error en la importación: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        } finally {
            if ($reader) {
                $reader->close();
            }
        }
    }

    //DSPACE 7
    public function dataTable()
    {
        $records=Estudiante::selectRaw('
                estudiantes.id,p.nombres,p.apellido_paterno,p.apellido_materno,pp.descripcion as perfil,
                td.abreviatura as tipo_documento,p.numero_documento
            ')
            ->join('usuarios as u','u.id','=','estudiantes.id_usuario')
            ->join('personas as p','p.id','=','u.id_persona')
            ->join('tipo_documentos_identidades as td','td.id','=','p.id_tipo_documento_identidad')
            ->join('perfiles as pp','pp.id','=','u.id_perfil')
            ->where('estudiantes.estado','A')
            ->where('u.id_perfil',Variable::ESTUDIANTE)
            ->orderBy('estudiantes.id','desc');
        return DataTables::of($records)
            ->filterColumn('nombres', function($query, $keyword) {
                $sql = "upper(p.nombres)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('apellido_paterno', function($query, $keyword) {
                $sql = "upper(p.apellido_paterno)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })->filterColumn('apellido_materno', function($query, $keyword) {
                $sql = "upper(p.apellido_materno)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('numero_documento', function($query, $keyword) {
                $sql = "upper(p.numero_documento)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('perfil', function($query, $keyword) {
                $sql = "upper(pp.descripcion)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('tipo_documento', function($query, $keyword) {
                $sql = "upper(td.abreviatura)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->toJson();
    }
}
