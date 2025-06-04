<?php

namespace App\Http\Controllers;

use App\Models\BolsaLaboral;
use App\Models\ProgramaEstudio;
use App\Models\Ubigeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Inertia\Inertia;

class OfertaLaboralController extends Controller
{

    public function index(Request $request)
    {
        set_time_limit(0);

        // Verificar el prefijo de ruta
        $prefix = $request ? ltrim($request->route()->getPrefix(), '/') : '';

        // Filtros
        $modalidad = $request->input('modalidad', null);
        $nivelTrabajo = $request->input('nivel_trabajo', null);
        $horario = $request->input('horario', null);
        $apto_discapacitados = $request->input('apto_discapacitados', null);
        $id_programa_estudio = $request->input('id_programa_estudio', null);
        $departamento = $request->input('departamento', null);
        $provincia = $request->input('provincia', null);

        // Construir la consulta con Query Builder
        $query = DB::table('bolsas_laborales as bl')
            ->selectRaw("
            bl.id, bl.uuid, bl.fecha_inicio, bl.fecha_fin, bl.numero_vacantes, bl.puesto,
            COALESCE(e.razon_social, e.nombre_comercial) AS empresa,
            bl.modalidad_trabajo, bl.apto_discapacitados, bl.requisitos,
            dd.nombre AS departamento, p.nombre AS provincia
        ")
            ->join('empresas as e', 'e.id', '=', 'bl.id_empresa')
            ->leftJoin('bolsas_programa_estudios as bpe', 'bpe.id_bolsa_laboral', '=', 'bl.id')
            ->leftJoin('programa_estudios as pe', 'bpe.id_programa_estudio', '=', 'pe.id')
            ->leftJoin('ubigeos as d', 'd.id', '=', 'bl.id_ubigeo')
            ->leftJoin('ubigeos as p', function ($join) {
                $join->on('p.cod_dpto', '=', 'd.cod_dpto')
                    ->on('p.cod_prov', '=', 'd.cod_prov')
                    ->where('p.cod_dist', '=', '00');
            })
            ->leftJoin('ubigeos as dd', function ($join) {
                $join->on('dd.cod_dpto', '=', 'd.cod_dpto')
                    ->where('dd.cod_prov', '=', '00')
                    ->where('dd.cod_dist', '=', '00');
            })
            ->where('bl.estado', 'A')
            ->where('bl.vigencia', 'VIGENTE')
            ->where('e.estado', 'A')
            ->where('bpe.estado', 'A')
            ->where('pe.estado', 'A')
            ->where('bl.fecha_inicio', '<=', now())
            ->where('bl.fecha_fin', '>=', now());

        // Aplicar filtros
        if ($modalidad) {
            $query->where('bl.modalidad_trabajo', $modalidad);
        }
        if ($nivelTrabajo) {
            $query->where('bl.nivel_trabajo', $nivelTrabajo);
        }
        if ($horario) {
            $query->where('bl.horario', $horario);
        }
        if ($apto_discapacitados) {
            $query->where('bl.apto_discapacitados', $apto_discapacitados);
        }
        if ($id_programa_estudio) {
            $query->where('bpe.id_programa_estudio', $id_programa_estudio);
        }
        if ($departamento) {
            $query->where('dd.cod_dpto', $departamento);
        }
        if ($provincia) {
            $query->where('p.cod_prov', $provincia);
        }

        // Agrupar y ordenar resultados
        $query->groupBy(
            'bl.id', 'bl.fecha_inicio', 'bl.fecha_fin', 'bl.numero_vacantes', 'bl.puesto',
            'bl.modalidad_trabajo', 'bl.apto_discapacitados', 'bl.requisitos',
            'dd.nombre', 'p.nombre', 'e.razon_social', 'e.nombre_comercial', 'bl.uuid'
        );
        $query->orderBy('bl.fecha_inicio', 'DESC')->orderBy('bl.created_at', 'DESC');

        // Aplicar paginación
        $records = $query->paginate(10); // 10 registros por página

        // Mapear los datos para el formato deseado
        $records->getCollection()->transform(function ($record) {
            $fecha_actual = new \DateTime();
            $fecha_inicio = new \DateTime($record->fecha_inicio);
            $fecha_fin = new \DateTime($record->fecha_fin);

            $dias_diferencia_inicio_fin = $fecha_inicio->diff($fecha_fin)->days;
            $dias_diferencia_actual_inicio = $fecha_actual->diff($fecha_inicio)->days;

            $tiempo = '';
            if ($dias_diferencia_actual_inicio < 1) {
                $tiempo = "Publicado hoy";
            } elseif ($dias_diferencia_actual_inicio == 1) {
                $tiempo = "Publicado hace 1 día";
            } elseif ($dias_diferencia_actual_inicio < 7) {
                $tiempo = "Publicado hace " . $dias_diferencia_actual_inicio . ' días';
            } else {
                $tiempo = "Publicado hace más de 1 semana";
            }

            return [
                'uuid' => $record->uuid,
                'puesto' => ucfirst(mb_strtolower($record->puesto ?: 'Oportunidad laboral', 'UTF-8')),
                'empresa' => $record->empresa,
                'apto_discapacidad' => $record->apto_discapacitados == 'A',
                'ubicacion' => ucfirst(mb_strtolower($record->departamento, 'UTF-8')) . ', ' . ucfirst(mb_strtolower($record->provincia, 'UTF-8')),
                'requisitos' => ucfirst(mb_strtolower($record->requisitos ?: 'No especifica', 'UTF-8')),
                'modalidad' => ucfirst(mb_strtolower($record->modalidad_trabajo ?: 'No especifica', 'UTF-8')),
                'postulacion_rapida' => $dias_diferencia_inicio_fin <= 7,
                'multiples_vacantes' => $record->numero_vacantes > 1,
                'nuevo' => $dias_diferencia_actual_inicio <= 1,
                'tiempo' => $tiempo,
            ];
        });

        // Retornar la vista con la paginación
        return Inertia::render('JobBoard/JobOffers', [
            'urlRoute' => $prefix,
            'departamentos' => Ubigeo::selectCombo()->selectDepartamento()->get(),
            'ofertas' => $records,
            'programa_estudios' => ProgramaEstudio::where('estado', 'A')->get(),
        ]);
    }

    public function getById($uuid)
    {
        set_time_limit(0);

        $sql = "
            SELECT
                bl.id,
                bl.uuid,
                e.id AS id_empresa,
                e.ruc,
                e.telefono,
                e.email,
                bl.fecha_inicio,
                bl.fecha_fin,
                bl.numero_vacantes,
                bl.puesto,
                bl.descripcion_puesto,
                COALESCE(e.razon_social, e.nombre_comercial) AS empresa,
                bl.modalidad_trabajo,
                bl.nivel_trabajo,
                bl.horario,
                bl.salario,
                bl.apto_discapacitados,
                bl.requisitos,
                bl.funciones,
                bl.disponibilidad_viajar,
                dd.nombre AS departamento,
                p.nombre AS provincia
            FROM bolsas_laborales bl
            INNER JOIN empresas e ON e.id = bl.id_empresa
            LEFT JOIN bolsas_programa_estudios bpe ON bpe.id_bolsa_laboral = bl.id
            LEFT JOIN programa_estudios pe ON bpe.id_programa_estudio = pe.id
            LEFT JOIN ubigeos d ON d.id = bl.id_ubigeo
            LEFT JOIN ubigeos p ON (p.cod_dpto = d.cod_dpto AND p.cod_prov = d.cod_prov AND p.cod_dist = '00')
            LEFT JOIN ubigeos dd ON (dd.cod_dpto = d.cod_dpto AND dd.cod_prov = '00' AND dd.cod_dist = '00')
            WHERE bl.estado = 'A'
                AND bl.vigencia = 'VIGENTE'
                AND e.estado = 'A'
                AND bpe.estado = 'A'
                AND pe.estado = 'A'
                AND DATE(bl.fecha_inicio) <= DATE(NOW())
                AND DATE(NOW()) <= DATE(bl.fecha_fin)
                AND bl.uuid = :uuid
            GROUP BY bl.id, bl.uuid, e.ruc, bl.fecha_inicio, bl.fecha_fin, bl.numero_vacantes, bl.puesto,
                     bl.descripcion_puesto, bl.modalidad_trabajo, bl.nivel_trabajo, bl.horario, bl.salario,
                     bl.apto_discapacitados, bl.disponibilidad_viajar, bl.requisitos, bl.funciones, e.telefono,
                     e.email, departamento, provincia, empresa, e.id";

        $params['uuid'] = $uuid;

        $record = DB::selectOne($sql, $params);
        $id_empresa=0;
        if ($record) {
            $id_empresa  = $record->id_empresa;
            list($dias_diferencia_actual_inicio,$dias_diferencia_inicio_fin,$tiempo)=BolsaLaboral::getTiempo($record->fecha_inicio,$record->fecha_fin);
            $record = [
                'uuid' => $record->uuid,
                'puesto' => ucfirst(mb_strtolower($record->puesto ?: 'Oportunidad laboral', 'UTF-8')),
                'ruc' => $record->ruc,
                'telefono' => $record->telefono ?: 'No especifica',
                'correo' => ucfirst(mb_strtolower($record->email ?: 'No especifica', 'UTF-8')),
                'empresa' => $record->empresa,
                'apto_discapacidad' => $record->apto_discapacitados == 'A',
                'disponibilidad_viajar' => $record->disponibilidad_viajar == 'A',
                'descripcion_puesto' => ucfirst(mb_strtolower($record->descripcion_puesto ?: 'No especifica', 'UTF-8')),
                'ubicacion' => ucfirst(mb_strtolower($record->departamento, 'UTF-8')) . ', ' . ucfirst(mb_strtolower($record->provincia, 'UTF-8')),
                'requisitos' => ucfirst(mb_strtolower($record->requisitos ?: 'No especifica', 'UTF-8')),
                'funciones' => ucfirst(mb_strtolower($record->funciones ?: 'No especifica', 'UTF-8')),
                'modalidad' => ucfirst(mb_strtolower($record->modalidad_trabajo ?: 'No especifica', 'UTF-8')),
                'nivel' => ucfirst(mb_strtolower($record->nivel_trabajo ?: 'No especifica', 'UTF-8')),
                'horario' => ucfirst(mb_strtolower($record->horario ?: 'No especifica', 'UTF-8')),
                'salario' => ucfirst(mb_strtolower($record-> salario ? 'S/.' . $record->salario : 'No especifica', 'UTF-8')),
                'postulacion_rapida' => $dias_diferencia_inicio_fin <= 7,
                'multiples_vacantes' => $record->numero_vacantes > 1,
                'numero_vacantes' => $record-> numero_vacantes . ' vacantes disponibles' ?: 'No especifica',
                'nuevo' => $dias_diferencia_actual_inicio <= 1,
                'tiempo' => $tiempo,
            ];
        }

        $sql_2 = "
            SELECT
                bl.id,
                bl.uuid,
                bl.fecha_inicio,
                bl.fecha_fin,
                bl.numero_vacantes,
                bl.puesto,
                COALESCE(e.razon_social, e.nombre_comercial) AS empresa,
                bl.modalidad_trabajo,
                bl.apto_discapacitados,
                bl.requisitos,
                dd.nombre AS departamento,
                p.nombre AS provincia
            FROM bolsas_laborales bl
            INNER JOIN empresas e ON e.id = bl.id_empresa
            LEFT JOIN bolsas_programa_estudios bpe ON bpe.id_bolsa_laboral = bl.id
            LEFT JOIN ubigeos d ON d.id = bl.id_ubigeo
            LEFT JOIN ubigeos p ON (p.cod_dpto = d.cod_dpto AND p.cod_prov = d.cod_prov AND p.cod_dist = '00')
            LEFT JOIN ubigeos dd ON (dd.cod_dpto = d.cod_dpto AND dd.cod_prov = '00' AND dd.cod_dist = '00')
            WHERE bl.estado = 'A'
                AND bl.vigencia = 'VIGENTE'
                AND e.estado = 'A'
                AND bpe.estado = 'A'
                AND bl.fecha_inicio <= NOW()
                AND NOW() <= bl.fecha_fin
                AND bl.uuid != :uuid
                AND bl.id_empresa = :id_empresa
            GROUP BY bl.id, bl.fecha_inicio, bl.fecha_fin, bl.numero_vacantes, bl.puesto, bl.modalidad_trabajo,
                    bl.apto_discapacitados, bl.requisitos, departamento, provincia, empresa
            ORDER BY bl.fecha_inicio DESC, bl.created_at DESC";

        $params_2['uuid'] = $uuid;
        $params_2['id_empresa'] = $id_empresa;

        $records_2 = DB::select($sql_2, $params_2);
        $records_2=BolsaLaboral::getRecords($records_2);
        return Inertia::render('JobBoard/JobOffer',[
            'oferta' => $record,
            'ofertas_relacionadas' => $records_2,
            'programa_estudios' => ProgramaEstudio::where('estado','A')->get(),
        ]);
    }

}
