<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')
                ->constrained('usuarios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_sede')
                ->constrained('sedes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_programa_estudio')
                ->constrained('programa_estudios')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreignId('id_plan_estudio')
                ->constrained('plan_estudios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_tipo_estudiante')
                ->constrained('tipo_estudiantes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_lengua_materna')
                ->constrained('lenguas_maternas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_pais')
                ->constrained('paises')
                ->onDelete('cascade')->onUpdate('cascade');
            //PROCEDENCIA EDUCACION EDUCATIVA
            $table->foreignId('id_modalidad_educativa')
                ->constrained('modalidades_educativas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_nivel_educativa')
                ->constrained('nivel_educativas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_tipo_gestion_educativa')
                ->constrained('tipo_gestion_educativas')
                ->onDelete('cascade')->onUpdate('cascade');
            //PROCEDENCIA EDUCACION EDUCATIVA
            $table->bigInteger('id_ubigeo')->index();//UBIGEO VIVE
            $table->string('direccion',300)->nullable();
            $table->string('celular',30)->nullable();
            $table->string('discapacidad',1)->comment('S,N');
            $table->string('menor_edad',1)->comment('S,N');// si es menor apoderado
            $table->string('codigo_modular',20)->nullable();
            $table->string('institucion',300)->nullable();
            $table->bigInteger('id_ubigeo_institucion')->index();
            $table->string('direccion_institucion',300)->nullable();
            $table->integer('anio_egreso_institucion');
            $table->integer('id_ciclo')->index(0);
            $table->integer('id_periodo_ingreso')->index(0);//SE VA REGISTRAR DE HACER LA MATRICULA INGRESANTE
            $table->string('estado_matricula',10)->comment('INGRESANTE,REGULAR,RETIRADO,TRANSLADADO,EGRESADO');
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
        Schema::dropIfExists('estudiantes');
    }
}
