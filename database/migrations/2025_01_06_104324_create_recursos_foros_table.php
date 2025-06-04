<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecursosForosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursos_foros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_seccion_recurso')
                ->constrained('secciones_recursos');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->integer('semana');
            $table->string('modo_calificacion');//MAXIMA/PROMEDIO
            $table->decimal('puntaje',20,2);
            $table->integer('maximo_comentarios');
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
        Schema::dropIfExists('recursos_foros');
    }
}
