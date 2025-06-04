<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudiantesApoderadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiantes_apoderados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_apoderado')
                ->constrained('apoderados')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_estudiante')
                ->constrained('estudiantes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('representante',1)->nullable();
            $table->string('parentesco',15)->nullable();
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
        Schema::dropIfExists('estudiantes_apoderados');
    }
}
