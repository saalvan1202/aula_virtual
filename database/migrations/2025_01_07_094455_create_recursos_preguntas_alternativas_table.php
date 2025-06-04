<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecursosPreguntasAlternativasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursos_preguntas_alternativas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_recurso_examen_pregunta')
                ->constrained('recursos_examenes_preguntas');
            $table->text('descripcion')->nullable();
            $table->text('respuesta')->nullable();//EMPAREJAMIENTO
            $table->decimal('porcentaje',20,2)->nullable();//SELECCION MULTIPLE
            $table->string('correcta',1)->nullable();//S,N
            $table->string('distractor',1)->nullable();//S,N//EMPAREJAMIENTO
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
        Schema::dropIfExists('recursos_preguntas_alternativas');
    }
}
