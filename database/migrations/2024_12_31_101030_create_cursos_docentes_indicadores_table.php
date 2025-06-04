<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursosDocentesIndicadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos_docentes_indicadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_curso_docente')
                ->constrained('cursos_docentes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('codigo',50)->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('semana_inicio');
            $table->integer('semana_fin');
            $table->decimal('porcentaje',20,2);
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
        Schema::dropIfExists('cursos_docentes_indicadores');
    }
}
