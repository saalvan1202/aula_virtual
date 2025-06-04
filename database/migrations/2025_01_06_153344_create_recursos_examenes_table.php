<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecursosExamenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursos_examenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_seccion_recurso')
                ->constrained('secciones_recursos');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->integer('duracion');
            $table->integer('semana');
            $table->integer('numero_intento');
            $table->string('barajar',1);//S,N
            $table->string('recuperacion',1);//S,N
            $table->string('tiempo_pregunta',1);//S,N
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
        Schema::dropIfExists('recursos_examenes');
    }
}
