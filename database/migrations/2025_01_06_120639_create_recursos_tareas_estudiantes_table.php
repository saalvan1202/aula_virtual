<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecursosTareasEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursos_tareas_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_recurso_tarea')
                ->constrained('recursos_tareas');
            $table->foreignId('id_curso_matricula')//aca esta el id matricula, y en matricula el id del estudiante
                ->constrained('cursos_matriculas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->date('fecha_calificacion')->nullable();
            $table->string('estado_calificacion',15);//PENDIENTE,CALIFICADO,REAPERTURADO
            $table->decimal('puntaje',20,2);
            $table->string('tipo',10)->nullable();//ARCHIVO,URL
            $table->text('url')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado_envio',10)->nullable();//PENDIENTE,PRESENTADO
            $table->string('estado',1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recursos_tareas_estudiantes');
    }
}
