<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursosDocentesSeccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos_docentes_secciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_curso_docente')
                ->constrained('cursos_docentes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('nombre',100)->nullable();
            $table->string('descripcion',250)->nullable();
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
        Schema::dropIfExists('cursos_docentes_secciones');
    }
}
