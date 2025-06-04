<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsistenciasEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistencias_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_clase_docente')
                ->constrained('clases_docentes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_curso_matricula')
                ->constrained('cursos_matriculas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('tipo_asistencia',50);//PRESENTE,FALTA,TARDE,JUSTIFICADO
            $table->string('observaciones',250)->nullable();
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
        Schema::dropIfExists('asistencias_estudiantes');
    }
}
