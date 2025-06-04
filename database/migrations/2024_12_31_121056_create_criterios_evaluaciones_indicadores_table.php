<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriteriosEvaluacionesIndicadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criterios_evaluaciones_indicadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_curso_docente_indicador')
                ->constrained('cursos_docentes_indicadores')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('nombre',100);
            $table->string('descripcion',200)->nullable();
            $table->integer('semana');
            $table->decimal('porcentaje',20,2);
            $table->string('tipo_evaluacion',50)->nullable();//CRITERIO,SUSTITUTORIO
            $table->string('estado_calificacion',15)->nullable();//PENDIENTE,CALIFICADO,PUBLICADO
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
        Schema::dropIfExists('criterios_evaluaciones_indicadores');
    }
}
