<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeccionRecurso extends Model
{
    protected $table = 'secciones_recursos';
    protected $fillable = ['id_curso_docente_seccion', 'id_tipo_recurso', 'nombre', 'descripcion', 'tipo', 'url', 'referencia', 'completado'];

    public function cursoDocenteSeccion()
    {
        return $this->belongsTo(CursoDocenteSeccion::class,'id_curso_docente_seccion');
    }

    public function tipoRecurso()
    {
        return $this->belongsTo(TipoRecurso::class,'id_tipo_recurso');
    }
    public function recursoTarea()
    {
        return $this->hasOne(RecursoTarea::class, 'id_seccion_recurso');
    }
    public function recursoForo()
    {
        return $this->hasOne(RecursoForo::class, 'id_seccion_recurso');
    }
    public function recursoExamen()
    {
        return $this->hasOne(RecursoExamen::class, 'id_seccion_recurso');
    }
    public static function getTipo()
    {
        return array_combo([
            'ARCHIVO'=>'ARCHIVO',
            'URL'=>'URL'
        ]);
    }
    public function scopeWithTipoRecurso($query)
    {
        return $query->with(['tipoRecurso'=>function($q){
            $q->where('estado', 'A')
                ->select(
                    'id', // Clave primaria
                    'nombre as tipo_nombre', // Alias para 'nombre'
                    'icono', // Este campo también se incluye sin alias
                    'color'
                );
        }]);
    }
    
    public function scopeWithRecursoExamen($query,$idCursoMatricula=0)
    {
        return $query->with(['recursoExamen'=>function($q) use ($idCursoMatricula) {
            $q->where('estado', 'A')
                ->select(
                    'id',
                    'id_seccion_recurso',
                    'fecha_inicio',
                    'fecha_fin',
                    'duracion',
                    'semana',
                    'numero_intento',
                    'barajar',
                    'recuperacion',
                    'tiempo_pregunta',
                    'estado',
                )->caducadoNoDisponible()
                ->withEstudianteExamen($idCursoMatricula);
        }]);
    }
    public function scopeWithRecursoForo($query,$idCursoMatricula=0)
    {
        return $query->with(['recursoForo'=>function($q) use ($idCursoMatricula){
            $q->where('estado', 'A')
                ->select(
                    'id', // Clave primaria
                    'id_seccion_recurso', // Relación con SeccionRecurso
                    'fecha_inicio', // Fecha de inicio
                    'fecha_fin', // Fecha de fin
                    'semana', // Tipo de calificación
                    'modo_calificacion', // Tipo de archivos permitidos
                    'puntaje',
                    'maximo_comentarios', // Número de intentos permitidos
                )->caducadoNoDisponible()->when($idCursoMatricula!=0,function($q) use ($idCursoMatricula){
                    $q->with(['recursoForoRespuesta' => function ($query) use ($idCursoMatricula) {
                        $query->where('id_curso_matricula', $idCursoMatricula)
                            ->where('estado', 'A')
                            ->orderBy('created_at', 'asc') // Ordenar por fecha de creación en orden ascendente
                            ->select(
                                'id',
                                'id_recurso_foro',
                                'id_curso_matricula',
                                'puntaje',
                                'estado_calificacion',
                                'estado',
                                'descripcion',
                                'fecha_calificacion',
                                'created_at' // Asegúrate de seleccionar el campo created_at
                            );
                        }
                    ]);
                });
        }]);
    }
    public function scopeWithRecursoTarea($query,$idCursoMatricula=0)
    {
        return $query->with(['recursoTarea' => function ($query) use ($idCursoMatricula) {
            $query->where('estado', 'A')->with('archivos')
                ->select(
                    'id', // Clave primaria
                    'id_seccion_recurso', // Relación con SeccionRecurso
                    'fecha_inicio', // Fecha de inicio
                    'fecha_fin', // Fecha de fin
                    'tipo_calificacion', // Tipo de calificación
                    'tipo_archivos', // Tipo de archivos permitidos
                    'numero_intento', // Número de intentos permitidos
                    'puntaje',
                )->caducadoNoDisponible()
                ->withTareaEstudiante($idCursoMatricula);
        }]);
    }

}
