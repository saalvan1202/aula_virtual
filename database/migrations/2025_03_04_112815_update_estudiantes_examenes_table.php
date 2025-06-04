<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEstudiantesExamenesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('estudiantes_examenes', function (Blueprint $table) {
            // Cambiar fecha_inicio de date a dateTime y hacerla nullable
            $table->dateTime('fecha_inicio')->nullable()->change();

            // Cambiar fecha_fin de date a dateTime y hacerla nullable
            $table->dateTime('fecha_fin')->nullable()->change();

            // Agregar el campo fecha_calificacion como dateTime y hacerla nullable
            $table->dateTime('fecha_calificacion')->nullable()->after('fecha_fin');

            // Hacer la columna duracion nullable
            $table->time('duracion')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('estudiantes_examenes', function (Blueprint $table) {
            // Revertir los cambios en caso de rollback
            $table->date('fecha_inicio')->nullable(false)->change(); // No nullable
            $table->date('fecha_fin')->nullable(false)->change();   // No nullable
            $table->dropColumn('fecha_calificacion');

            // Revertir duracion a no nullable
            $table->time('duracion')->nullable(false)->change();
        });
    }
}
