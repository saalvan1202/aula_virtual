<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursosMatriculasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos_matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_matricula')
                ->constrained('matriculas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_curso_docente')
                ->constrained('cursos_docentes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->date('fecha_nota')->nullable();
            $table->decimal('nota',10,2)->nullable();
            $table->string('estado_nota')->nullable();//APROBADO, DESAPROBADO
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
        Schema::dropIfExists('cursos_matriculas');
    }
}
