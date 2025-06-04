<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTiposRecursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipos_recursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',80);
            $table->string('descripcion',200);
            $table->string('slug',50)->index();
            $table->string('icono',20);
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        DB::table('tipos_recursos')->insert([
            [
                'nombre'=>'Archivo',
                'descripcion'=>'Permite a los profesores proveer un archivo como un recurso del curso',
                'slug'=>'secciones_recursos',
                'icono'=>'FileIcon',
                'estado'=>'I',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'nombre'=>'Link/Url',
                'descripcion'=>'Permite que el profesor pueda proporcionar un enlace de Internet como un recurso del curso',
                'slug'=>'secciones_recursos',
                'icono'=>'LinkIcon',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'nombre'=>'Tareas',
                'descripcion'=>'Permite a un profesor evaluar el aprendizaje de los alumnos mediante la creación de una tarea a realizar que luego revisará, valorará, calificará y a la que podrá dar retroalimentación.',
                'slug'=>'recursos_tareas',
                'icono'=>'BookOpenIcon',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'nombre'=>'Foros',
                'descripcion'=>'Permite a los participantes tener discusiones asincrónicas, es decir discusiones que tienen lugar durante un período prolongado de tiempo.',
                'slug'=>'recursos_foros',
                'icono'=>'BookOpenIcon',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'nombre'=>'Chat',
                'descripcion'=>'Permite a los participantes tener una discusión en formato texto de manera sincrónica en tiempo real.',
                'slug'=>'recursos_chats',
                'icono'=>'BookOpenIcon',
                'estado'=>'I',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'nombre'=>'Examenes',
                'descripcion'=>'Permite al profesor diseñar y plantear cuestionarios con preguntas tipo opción múltiple, verdadero/falso, coincidencia, respuesta corta y respuesta numérica',
                'slug'=>'recursos_examenes',
                'icono'=>'EditIcon',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipos_recursos');
    }
}
