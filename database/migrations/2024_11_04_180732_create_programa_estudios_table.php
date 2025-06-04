<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramaEstudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programa_estudios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo',20)->nullable();
            $table->string('descripcion',100);
            $table->string('nivel_formativo',100)->nullable();
            $table->string('actividad_economica',100)->nullable();
            $table->string('duracion',100)->nullable();
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
        Schema::dropIfExists('programas_estudios');
    }
}
