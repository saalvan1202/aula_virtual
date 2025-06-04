<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursosDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos_docentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_periodo_clases')
                ->constrained('periodo_clases')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_sede')
                ->constrained('sedes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_turno')
                ->constrained('turnos')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_seccion')
                ->constrained('secciones')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_docente')
                ->constrained('usuarios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_cursos')
                ->constrained('cursos')
                ->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('cursos_docentes');
    }
}
