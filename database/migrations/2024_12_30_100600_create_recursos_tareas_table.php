<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecursosTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursos_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_seccion_recurso')
                ->constrained('secciones_recursos');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->string('tipo_archivos',50)->nullable();
            $table->integer('numero_intento');
            $table->string('grupal',1)->default('N');
            $table->string('tipo_calificacion',70)->nullable();//SIMPLE
            $table->integer('puntaje')->default('20');
            $table->string('recuperacion',1);//N, esto no va estar form
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
        Schema::dropIfExists('recursos_tareas');
    }
}
