<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecursosForosRespuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursos_foros_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_recurso_foro')
                ->constrained('recursos_foros');
            $table->foreignId('id_curso_matricula')//aca esta el id matricula, y en matricula el id del estudiante
            ->constrained('cursos_matriculas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->integer('id_padre')->index();
            $table->text('descripcion')->nullable();
            $table->date('fecha_calificacion')->nullable();
            $table->string('estado_calificacion',10);//PENDIENTE,CALIFICADO
            $table->decimal('puntaje',20,2);
            $table->string('tipo',10)->nullable();//ARCHIVO,URL
            $table->text('url')->nullable();

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
        Schema::dropIfExists('recursos_foros_respuestas');
    }
}
