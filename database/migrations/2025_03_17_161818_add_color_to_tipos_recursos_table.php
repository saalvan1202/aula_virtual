<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddColorToTiposRecursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Agregar el campo 'color' a la tabla 'tipos_recursos'
        Schema::table('tipos_recursos', function (Blueprint $table) {
            $table->string('color', 20)->after('icono')->nullable();
        });

        // Actualizar los registros existentes con los colores correspondientes
        DB::table('tipos_recursos')->where('nombre', 'Archivo')->update(['color' => '#FF5733']);
        DB::table('tipos_recursos')->where('nombre', 'Link/Url')->update(['color' => '#20B2AA']);
        DB::table('tipos_recursos')->where('nombre', 'Tareas')->update(['color' => '#1E75C3']);
        DB::table('tipos_recursos')->where('nombre', 'Foros')->update(['color' => '#6A5ACD']);
        DB::table('tipos_recursos')->where('nombre', 'Chat')->update(['color' => '#A133FF']);
        DB::table('tipos_recursos')->where('nombre', 'Examenes')->update(['color' => '#646E8C']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar el campo 'color' si se revierte la migración
        Schema::table('tipos_recursos', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}