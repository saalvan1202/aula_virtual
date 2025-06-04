<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePuntajePrecisionInEstudiantesExamenesRespuestas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estudiantes_examenes_respuestas', function (Blueprint $table) {
            $table->decimal('puntaje', 20, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estudiantes_examenes_respuestas', function (Blueprint $table) {
            $table->decimal('puntaje', 20, 2)->change();
        });
    }
}
