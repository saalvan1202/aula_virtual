<?php

namespace App\Models;

use App\Services\Variable;
use App\Traits\ScopeCaducadoNoDisponible;
use Illuminate\Database\Eloquent\Model;

class RecursoExamen extends Model
{
    use ScopeCaducadoNoDisponible;
    protected $table='recursos_examenes';
    protected $fillable=[
        'fecha_inicio','fecha_fin','duracion','semana','numero_intento','barajar',
        'recuperacion','tiempo_pregunta','fecha_calificacion'
    ];

    public function preguntas()
    {
        return $this->hasMany(RecursoExamenPregunta::class,'id_recurso_examen');
    }
    public function seccionRecurso()
    {
        return $this->belongsTo(SeccionRecurso::class,'id_seccion_recurso');
    }
    public function estudianteExamen()
    {
        return $this->hasMany(EstudianteExamen::class, 'id_recurso_examen');
    }

    public static function getSeccionRecurso($id_recurso_examen)
    {
        return static::selectRaw("sr.id,sr.mostrar,sr.estado")
        ->join('secciones_recursos as sr','sr.id','=','recursos_examenes.id_seccion_recurso')
        ->where('recursos_examenes.id',$id_recurso_examen)
        ->first();
    }
    public static function getRecursoExamenConPreguntas($id, $id_perfil, $permiso_estudiante = false) 
    {
        return static::where('id', $id)
            ->with('seccionRecurso')
            ->with([
                'preguntas' => function ($query) use ($id_perfil, $permiso_estudiante) {
                    $query->where('estado', 'A')
                        ->orderBy('created_at', 'asc')
                        ->select(
                            'id',
                            'id_recurso_examen',
                            'id_tipo_pregunta',
                            'descripcion',
                            'duracion',
                            'puntaje',
                        )->with([
                            'tipoPregunta',
                            'alternativas' => function ($query) use ($id_perfil, $permiso_estudiante) {
                                $selectFields = [
                                    'id',
                                    'id_recurso_examen_pregunta',
                                    'descripcion',
                                    'created_at'
                                ];

                                $is_teacher = Variable::isTeacher($id_perfil);

                                // Solo incluir el campo 'correcta' si $is_teacher es true
                                if ($is_teacher || $permiso_estudiante) {
                                    $selectFields[] = 'correcta';
                                    $selectFields[] = 'porcentaje';
                                    $selectFields[] = 'estado';
                                }

                                $query->where('estado', 'A')
                                    ->orderBy('id', 'asc')
                                    ->select($selectFields);
                            },
                        ]);
                },
            ])
            ->first();
    }

    public function scopeWithEstudianteExamen($query,$idCursoMatricula)
    {
        return $query->with(['estudianteExamen' => function ($query) use($idCursoMatricula) {
            $query->where('id_curso_matricula', $idCursoMatricula)
                ->where('estado', 'A')
                ->orderBy('created_at', 'asc') // Ordenar por fecha de creación en orden ascendente
                ->select(
                    'id',
                    'id_recurso_examen',
                    'id_curso_matricula',
                    'puntaje',
                    'estado_examen',
                    'estado',
                    'fecha_calificacion',
                    'created_at' // Asegúrate de seleccionar el campo created_at
                );
        }]);
    }
}
