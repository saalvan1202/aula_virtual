<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_estudiante')
                ->constrained('estudiantes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_plan_estudio')// como historico, si el estudiante cambia de carrera
                ->constrained('plan_estudios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('tipo',30);//MODULAR,CERTIFICADO ESTUDIOS(TODO),CONSTANCIA NOTAS
            $table->integer('id_referencia')->index();//si es modular el id del modulo
            $table->date('fecha');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('director_general',150)->nullable();
            $table->string('numero_registro_institucional',70)->nullable();
            $table->string('numero_registro_minedu',70)->nullable();
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
        Schema::dropIfExists('certificados');
    }
}
