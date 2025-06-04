<?php

namespace App\Http\Controllers;

use App\Models\BolsaLaboral;
use App\Models\BolsaProgramaEstudio;
use App\Models\Empresa;
use App\Models\ProgramaEstudio;
use App\Models\Sede;
use App\Models\TipoAmbiente;
use App\Models\Ubigeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class BolsaLaboralController extends Controller
{

    public function index()
    {
        return Inertia::render('JobBoard/JobBoard',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'sedes'=>Sede::where('estado','A')->get(),
            'tipo_ambiente'=>TipoAmbiente::where('estado','A')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('JobBoard/AddJobBoard', [
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'departamentos'=>Ubigeo::selectCombo()->selectDepartamento()->get(),
            'programa_estudios' => ProgramaEstudio::where('estado','A')->get(),
            'isEdit' => false,
            'propuesta' => [],
            'menu_dependencia'=>[
                [
                    'menu'=>'bolsa_laborales',
                    'url'=>ltrim(request()->getPathInfo(),'/')
                ]
            ]
        ]);
    }

    public function edit($id)
    {
        $propuesta = BolsaLaboral::where('id', $id)
            ->where('estado', 'A')
            ->with(['empresa','bolsaProgramaEstudio.programaEstudio'])
            ->get()
            ->map(function ($propuesta) {
                $ubigeo = (new Ubigeo())->getFullUbigeo($propuesta->id_ubigeo);
                $bolsaProgramaEstudio = BolsaProgramaEstudio::where('id_bolsa_laboral', $propuesta->id)->get();
                $id_programa_estudio = $bolsaProgramaEstudio->isNotEmpty() ? $bolsaProgramaEstudio->first()->id_programa_estudio : null;

                $empresa = $propuesta->empresa;
                $descripcion_empresa = $empresa->ruc . ' - ' . ($empresa->razon_social ?? $empresa->nombre_comercial);

                return [
                    'id_propuesta' => $propuesta->id,
                    'id_programa_estudio' => $id_programa_estudio,
                    'fecha_inicio' => $propuesta->fecha_inicio,
                    'fecha_fin' => $propuesta->fecha_fin,
                    'empresa' => [
                        'id' => $empresa->id,
                        'ruc'=>$empresa->ruc,
                        'empresa' => $descripcion_empresa,
                        'razon_social'=>$empresa->razon_social,
                        'direccion'=>$empresa->direccion,
                        'correo'=>$empresa->correo,
                        'telefono'=>$empresa->telefono,
                        'pagina_web'=>$empresa->pagina_web
                    ],
                    'representante_legal' => $propuesta->empresa->representante_legal,
                    'ubigeo' => $ubigeo,
                    'direccion' => $propuesta->direccion,
                    'correo' => $propuesta->empresa->email,
                    'telefono' => $propuesta->empresa->telefono,
                    'pagina_web' => $propuesta->empresa->pagina_web,
                    'numero_vacantes' => $propuesta->numero_vacantes,
                    'puesto' => $propuesta->puesto,
                    'descripcion_puesto' => $propuesta->descripcion_puesto,
                    'modalidad_trabajo' => $propuesta->modalidad_trabajo,
                    'horario' => $propuesta->horario,
                    'salario' => $propuesta->salario,
                    'requisitos' => $propuesta->requisitos,
                    'nivel_trabajo' => $propuesta->nivel_trabajo,
                    'funciones' => $propuesta->funciones,
                    'link_postulacion' => $propuesta->link_postulacion,
                    'disponibilidad_viajar' => $propuesta->disponibilidad_viajar,
                    'apto_discapacitados' => $propuesta->apto_discapacitados,
                ];
            });


        return Inertia::render('JobBoard/AddJobBoard', [
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'departamentos'=>Ubigeo::selectCombo()->selectDepartamento()->get(),
            'programa_estudios' => ProgramaEstudio::where('estado','A')->get(),
            'isEdit' => true,
            'propuesta' => $propuesta,
            'menu_dependencia'=>[
                [
                    'menu'=>'bolsa_laborales',
                    'url'=>ltrim(request()->getPathInfo(),'/')
                ]
            ]
        ]);
    }

    public function store(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'id_empresa' => 'required|integer|min:1',
            'id_programa_estudio' => 'required|integer|min:1',
            'fecha_inicio' => 'required|date_format:Y-m-d',
            'fecha_fin' => 'required|date_format:Y-m-d',
            'puesto' => 'nullable|string|max:200',
            'descripcion_puesto' => 'nullable|string',
            'requisitos' => 'nullable|string',
            'funciones' => 'nullable|string',
            'id_ubigeo' => 'required|integer|min:1',
            'modalidad_trabajo' => 'nullable|string|in:PRESENCIAL,HÍBRIDO,REMOTO',
            'nivel_trabajo' => 'nullable|string|in:SIN EXPERIENCIA,TRAINEE,JUNIOR,SEMI-SENIOR,SENIOR',
            'horario' => 'nullable|string|max:100',
            'salario' => 'nullable|string|max:100',
            'numero_vacantes' => 'required|integer',
            'link_postulacion' => 'nullable|string|max:150|url',
            'disponibilidad_viajar' => 'nullable|string|in:A,I',
            'apto_discapacitados' => 'nullable|string|in:A,I',
        ], [], [
            'id_empresa' => 'empresa',
            'id_programa_estudio' => 'programa de estudio',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'id_ubigeo' => 'ubicación',
            'modalidad_trabajo' => 'modalidad de trabajo',
            'nivel_trabajo' => 'nivel de trabajo',
            'numero_vacantes' => 'número de vacantes',
            'link_postulacion' => 'link de postulación',
            'disponibilidad_viajar' => 'disponibilidad para viajar',
            'apto_discapacitados' => 'apto para discapacitados',
        ]);

        DB::transaction(function () use ($request) {
            $obj = BolsaLaboral::find($request->input('id'));
            if (is_null($obj)) {
                $obj = new BolsaLaboral();
                $obj->uuid = (string) Str::uuid();
                $obj->vigencia = 'VIGENTE';
                $obj->estado = 'A';
            }
            $obj->fill($request->except('id_programa_estudio'));
            $obj->save();

            $bolsaProgramaEstudio = new BolsaProgramaEstudio();

            // Asignar los valores a las propiedades del modelo
            $bolsaProgramaEstudio->id_bolsa_laboral = $obj->id;
            $bolsaProgramaEstudio->id_programa_estudio = $request->input('id_programa_estudio');
            $bolsaProgramaEstudio->estado = 'A';

            $bolsaProgramaEstudio->save();
        });

        return response()->json(['message' => 'Datos almacenados con éxito.']);
    }

    public function update(Request $request, $id)
    {
        set_time_limit(0);
        $request->validate([
            'id_empresa' => 'nullable|integer|min:1',
            'id_programa_estudio' => 'nullable|integer|min:1',
            'fecha_inicio' => 'nullable|date_format:Y-m-d',
            'fecha_fin' => 'nullable|date_format:Y-m-d',
            'puesto' => 'nullable|string|max:200',
            'descripcion_puesto' => 'nullable|string',
            'requisitos' => 'nullable|string',
            'funciones' => 'nullable|string',
            'id_ubigeo' => 'nullable|integer|min:1',
            'modalidad_trabajo' => 'nullable|string|in:PRESENCIAL,HÍBRIDO,REMOTO',
            'nivel_trabajo' => 'nullable|string|in:SIN EXPERIENCIA,TRAINEE,JUNIOR,SEMI-SENIOR,SENIOR',
            'horario' => 'nullable|string|max:100',
            'salario' => 'nullable|string|max:100',
            'numero_vacantes' => 'nullable|integer',
            'link_postulacion' => 'nullable|string|max:150|url',
            'disponibilidad_viajar' => 'nullable|string|in:A,I',
            'apto_discapacitados' => 'nullable|string|in:A,I',
        ], [], [
            'id_empresa' => 'empresa',
            'id_programa_estudio' => 'programa de estudio',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'id_ubigeo' => 'ubicación',
            'modalidad_trabajo' => 'modalidad de trabajo',
            'nivel_trabajo' => 'nivel de trabajo',
            'numero_vacantes' => 'número de vacantes',
            'link_postulacion' => 'link de postulación',
            'disponibilidad_viajar' => 'disponibilidad para viajar',
            'apto_discapacitados' => 'apto para discapacitados',
        ]);

        DB::transaction(function () use ($request, $id) {
            $obj = BolsaLaboral::findOrFail($id);

            // Actualizar los datos principales de la bolsa laboral
            $obj->fill($request->except('id_programa_estudio'));
            $obj->save();

            // Verificar si se cambió el id_programa_estudio
            $existingRecord = BolsaProgramaEstudio::where('id_bolsa_laboral', $obj->id)->first();
            if ($existingRecord) {
                if ($existingRecord->id_programa_estudio !== $request->input('id_programa_estudio')) {
                    // Actualizar el id_programa_estudio si es diferente
                    $existingRecord->id_programa_estudio = $request->input('id_programa_estudio');
                    $existingRecord->save();
                }
            }
        });

        return response()->json(['message' => 'Datos actualizados con éxito.']);
    }

    public function dataTable()
    {
        set_time_limit(0);
        $sql="SELECT
                bl.id,
                pe.descripcion AS programa_estudio,
                e.ruc || ' - ' || coalesce(e.razon_social, e.nombre_comercial) AS empresa,
                bl.fecha_inicio || ' - ' || bl.fecha_fin AS publicacion,
                e.email,
                bl.nivel_trabajo,
                e.telefono,
                e.pagina_web,
                bl.puesto,
                bl.horario,
                bl.numero_vacantes,
                dd.nombre || ', ' || p.nombre AS ubicacion
                FROM bolsas_laborales bl
                INNER JOIN empresas e ON e.id = bl.id_empresa
                LEFT JOIN bolsas_programa_estudios bpe ON bpe.id_bolsa_laboral = bl.id
                LEFT JOIN programa_estudios pe ON pe.id = bpe.id_programa_estudio
                LEFT JOIN ubigeos d ON d.id = bl.id_ubigeo
                LEFT JOIN ubigeos p ON (p.cod_dpto = d.cod_dpto AND p.cod_prov = d.cod_prov AND p.cod_dist = '00')
                LEFT JOIN ubigeos dd ON (dd.cod_dpto = d.cod_dpto AND dd.cod_prov = '00' AND dd.cod_dist = '00')
                WHERE bl.estado = 'A'
                  AND bl.vigencia = 'VIGENTE'
                  AND e.estado = 'A'
                  AND bpe.estado = 'A'
                  AND pe.estado = 'A'
                GROUP BY bl.id,e.email,e.telefono,e.pagina_web,bl.puesto,bl.horario,bl.numero_vacantes,
                         bl.nivel_trabajo,empresa,programa_estudio,publicacion,ubicacion";
        $records=DB::table(DB::raw("($sql) as tbl"));
        return DataTables::of($records)->toJson();
    }

    public function destroy($id)
    {
        // Buscar el objeto bolsas_laborales
        $obj = BolsaLaboral::findOrFail($id);

        // Cambiar el estado de la bolsa laboral a inactivo
        $obj->estado = 'I';
        $obj->vigencia = 'CONCLUIDO';
        $obj->update();

        // Marcar como inactivos los registros relacionados en la tabla de muchos a muchos
        BolsaProgramaEstudio::where('id_bolsa_laboral', $obj->id)
            ->update([
                'estado' => 'I', // Asumí que tienes un campo 'estado' en la tabla de relación, si no, puedes agregarlo o manejarlo de otra manera
            ]);

        return response()->json('ok');
    }

}
