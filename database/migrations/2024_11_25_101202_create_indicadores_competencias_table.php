<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndicadoresCompetenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicadores_competencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_modulo_competencia')
                ->constrained('modulos_competencias')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->integer('numero');
            $table->text('descripcion')->nullable();
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
        Schema::dropIfExists('indicadores_competencias');
    }
}
