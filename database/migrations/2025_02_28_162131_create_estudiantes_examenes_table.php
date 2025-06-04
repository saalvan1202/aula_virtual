<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudiantesExamenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiantes_examenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_recurso_examen')
                ->constrained('recursos_examenes');
            $table->foreignId('id_curso_matricula')//aca esta el id matricula, y en matricula el id del estudiante
                ->constrained('cursos_matriculas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->time('duracion');
            $table->decimal('puntaje',20,2);
            $table->string('ip',25)->nullable();
            $table->string('estado_examen',25)->nullable();//PENDIENTE,CALIFICADO
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
        Schema::dropIfExists('estudiantes_examenes');
    }
}
