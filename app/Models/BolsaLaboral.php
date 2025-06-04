<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BolsaLaboral extends Model
{
    protected $table='bolsas_laborales';
    protected $fillable=[
        'id_empresa','fecha_inicio','fecha_fin','puesto','descripcion_puesto',
        'requisitos','funciones','id_ubigeo','modalidad_trabajo','nivel_trabajo',
        'horario','salario','numero_vacantes','link_postulacion','disponibilidad_viajar',
        'apto_discapacitados','vigencia','direccion'
    ];

    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class, 'id_ubigeo');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
    public function bolsaProgramaEstudio()
    {
        return $this->hasMany(BolsaProgramaEstudio::class,'id_programa_estudio');
    }
    public static function getTiempo($inicio, $fin)
    {
        $fecha_actual = new \DateTime();
        $fecha_inicio = new \DateTime($inicio);
        $fecha_fin = new \DateTime($fin);

        // Calcular diferencia entre fechas
        $dias_diferencia_inicio_fin = $fecha_inicio->diff($fecha_fin)->days;
        $dias_diferencia_actual_inicio = $fecha_actual->diff($fecha_inicio)->days;

        // Calcular el tiempo desde la publicación
        $tiempo = '';
        if ($dias_diferencia_actual_inicio < 1) {
            $tiempo = "Publicado hoy";
        } elseif ($dias_diferencia_actual_inicio == 1) {
            $tiempo = "Publicado hace " . $dias_diferencia_actual_inicio . ' dia';
        } elseif ($dias_diferencia_actual_inicio < 7) {
            $tiempo = "Publicado hace ".$dias_diferencia_actual_inicio.' dias';
        } else {
            $tiempo = "Publicado hace más de 1 semana";
        }
        return[
            $dias_diferencia_actual_inicio,
            $dias_diferencia_inicio_fin,
            $tiempo
        ];
    }
    public static function getRecords($records): array
    {
        return collect($records)->map(function ($record) {
            list($dias_diferencia_actual_inicio,$dias_diferencia_inicio_fin,$tiempo)=static::getTiempo($record->fecha_inicio,$record->fecha_fin);
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
        })->toArray();
    }

}
