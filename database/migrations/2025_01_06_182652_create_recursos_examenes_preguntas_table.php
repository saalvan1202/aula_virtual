<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecursosExamenesPreguntasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursos_examenes_preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_recurso_examen')
                ->constrained('recursos_examenes');
            $table->foreignId('id_tipo_pregunta')
                ->constrained('tipos_preguntas');
            $table->text('descripcion')->nullable();
            $table->integer('duracion');
            $table->decimal('puntaje',20,2);
            $table->string('retroalimentacion_buena',250)->nullable();
            $table->string('retroalimentacion_mala',250)->nullable();
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
        Schema::dropIfExists('recursos_examenes_preguntas');
    }
}
