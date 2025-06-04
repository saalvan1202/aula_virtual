<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * @method static \Illuminate\Database\Eloquent\Builder joinPersona()
 */
class Estudiante extends Model
{
    use HasFactory,Notifiable;
    protected $table='estudiantes';
    protected $attributes=[
        'id_modalidad_educativa'=>1,
        'id_tipo_gestion_educativa'=>1,
        'id_lengua_materna'=>1,
        'id_ubigeo'=>1,
        'anio_egreso_institucion'=>2025,
        'id_ubigeo_institucion'=>1
    ];
    protected $fillable=[
        'id_sede','id_programa_estudio','id_plan_estudio','id_tipo_estudiante','id_lengua_materna',
        'id_pais','id_modalidad_educativa','id_nivel_educativa','id_tipo_gestion_educativa','id_ubigeo',
        'direccion','celular','discapacidad','menor_edad','codigo_modular','institucion','id_ubigeo_institucion',
        'direccion_institucion','anio_egreso_institucion','id_ciclo','estado_matricula','id_periodo_ingreso',
    ];
    public function autocomplete($texto)
    {
        $sql="select
            e.id,
            e.id_plan_estudio,
            e.id_ciclo,
            e.id_usuario,
            e.id_sede,
            p.numero_documento||' '||p.apellido_paterno||' '||p.apellido_materno ||' '||p.nombres as estudiante
            from
            usuarios as u
            inner join estudiantes as e on u.id=e.id_usuario inner join personas as p on p.id=u.id_persona
            where u.estado='A'
            and (p.numero_documento ilike ? or p.nombres ilike ? or p.apellido_paterno ilike ? or p.apellido_materno ilike ?)
            limit 10";
        return DB::select($sql,[$texto,$texto,$texto,$texto]);
    }
    public static function getAsitenciasEstudiantes($id_clase_docente,$id_curso_docente){
        $alumnos=DB::select("select
        u.id as id_usuario,
        p.apellido_paterno||' '||p.apellido_materno||' '||p.nombres as estudiante,
        tdi.abreviatura as tipo_documento,
        p.numero_documento,
        coalesce(ae.id,'-1') as id_asistencia,
        coalesce(ae.tipo_asistencia,'') as tipo_asistencia,
        coalesce(la.total_clases,0) as total_clases,
        coalesce(la.presente,0) as presente,
        coalesce(la.falta,0) as falta,
        coalesce(la.total_dictadas,0) as total_dictadas,
        0 as porcentaje_faltas,
        ae.observaciones,
        --cd.culminado,
        cm.id as id_curso_matricula,
        la.*
        from
        usuarios u
        inner join personas p on p.id=u.id_persona
        inner join tipo_documentos_identidades tdi on tdi.id=p.id_tipo_documento_identidad
        inner join estudiantes as e on e.id_usuario=u.id
        inner join matriculas as m on (m.id_estudiante=e.id and m.estado='A')
        inner join cursos_matriculas as cm on (cm.id_matricula=m.id and cm.estado='A')
        --inner join cursos_docentes as cd on cd.id=cm.id_curso_docente
        left join asistencias_estudiantes ae on (ae.id_curso_matricula=cm.id and ae.id_clase_docente=?)
        left join lateral(
        select 
            count(cd.id) as total_clases,
            count(ae.id) as total_dictadas,
            count( case when ae.tipo_asistencia='PRESENTE' then 1 end) as presente,
            count( case when ae.tipo_asistencia='FALTA' then 1 end) as falta
        from 
            clases_docentes as cd left join 
            asistencias_estudiantes as ae on(ae.id_curso_matricula=cm.id)
        where cd.id_curso_docente=cd.id and cd.estado='A'
        group by cd.id_curso_docente) 
        as la on true
        where cm.id_curso_docente=?",[$id_clase_docente,$id_curso_docente]);
        $alumnos=collect($alumnos)->map(function($item){
            $porcentaje=0;
            if($item->total_clases>0){
                $porcentaje=round(($item->falta/$item->total_clases)*100,2);
            }
            $item->porcentaje_faltas=$porcentaje;
            return $item;
        });  
        return $alumnos;
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function foto()
    {
        return $this->belongsTo(Archivo::class,'id','id_referencia')->where('referencia',static::getTable());
    }
    public function distrito()
    {
        return $this->belongsTo(Ubigeo::class,'id_ubigeo');
    }

    public function distritoI()
    {
        return $this->belongsTo(Ubigeo::class,'id_ubigeo_institucion');
    }
    public function detalleApoderado()
    {
        return $this->hasMany(EstudianteApoderado::class,'id_estudiante');
    }
    public function scopeJoinPersona($query)
    {
        return $query->join('usuarios','usuarios.id','=','estudiantes.id_usuario')->join('personas','personas.id','=','usuarios.id_persona')
            ->selectRaw('estudiantes.*,usuarios.telefono,usuarios.email,
                personas.id_tipo_documento_identidad,
                personas.nombres,
                personas.apellido_paterno,
                personas.apellido_materno,
                personas.numero_documento,
                personas.numero_documento,
                personas.sexo,
                personas.estado_civil,
                personas.fecha_nacimiento,
                personas.id as id_personas
            ');
    }
    public function scopeAutocomplete($query)
    {
        $query->join('usuarios','usuarios.id','=','estudiantes.id_usuario')
            ->join('personas','personas.id','=','usuarios.id_persona')
            ->selectRaw("
                estudiantes.id,
                estudiantes.id_plan_estudio,
                estudiantes.id_ciclo,
                estudiantes.id_usuario,
                estudiantes.id_sede,
                personas.nombres,
                personas.apellido_paterno,
                personas.apellido_materno,
                personas.numero_documento,
                personas.apellido_paterno||' '||personas.apellido_materno ||' '||personas.nombres as apellidos_completos,
                personas.numero_documento||' '||personas.apellido_paterno||' '||personas.apellido_materno ||' '||personas.nombres as estudiante
            ");
    }
    public function scopeWithApoderado($query)
    {
        return $query->with(['detalleApoderado'=>function($query){
            $query->selectRaw("
                estudiantes_apoderados.id_estudiante,
                estudiantes_apoderados.id_apoderado,
                estudiantes_apoderados.parentesco,
                personas.apellido_paterno||' '||personas.apellido_materno ||' '||personas.nombres as apoderado,
                personas.numero_documento,
                tdi.abreviatura as tipo_documento
            ")
                ->join('apoderados','apoderados.id','=','estudiantes_apoderados.id_apoderado')
                ->join('personas','personas.id','=','apoderados.id_persona')
                ->join('tipo_documentos_identidades as tdi','tdi.id','=','personas.id_tipo_documento_identidad')
                ->where('estudiantes_apoderados.estado','A');
        }]);
    }

}
