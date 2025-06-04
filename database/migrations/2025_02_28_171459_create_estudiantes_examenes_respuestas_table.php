<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudiantesExamenesRespuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiantes_examenes_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_estudiante_examen')
                ->constrained('estudiantes_examenes');
            $table->foreignId('id_recurso_examen_pregunta')
                ->constrained('recursos_examenes_preguntas');
            $table->bigInteger('id_recurso_pregunta_alternativa')->default(0)->index();//id_recursos_preguntas_alternativas //marcada
            $table->text('respuesta')->nullable();//para preguntas tipo TEXTO
            $table->string('calificado')->nullable();//S,N(por defecto S si la pregunta no es TEXTO)
            $table->decimal('puntaje',20,2);
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
        Schema::dropIfExists('estudiantes_examenes_respuestas');
    }
}
