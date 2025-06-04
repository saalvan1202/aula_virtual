<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBolsasLaborales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolsas_laborales', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->foreignId('id_empresa')
                ->constrained('empresas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('puesto',200)->nullable();
            $table->text('descripcion_puesto')->nullable();
            $table->text('requisitos')->nullable();
            $table->text('funciones')->nullable();
            $table->bigInteger('id_ubigeo')->index();
            $table->string('modalidad_trabajo',25)->nullable();//PRESENCIAL,FREELANCE
            $table->string('nivel_trabajo',25)->nullable();//JUNIOR,SEMI SENIOR,SENIOR,SIN EXPERENCIA
            $table->string('horario',100)->nullable();//PART-TIME, FULL-TIME
            $table->string('salario',100)->nullable();
            $table->integer('numero_vacantes');
            $table->string('link_postulacion',150)->nullable();
            $table->string('disponibilidad_viajar',1)->nullable();//S,N
            $table->string('apto_discapacitados',1)->nullable();//S,N
            $table->string('vigencia',70)->nullable();//VIGENTE,PROCESO,CONCLUIDO
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
        Schema::dropIfExists('bolsas_laborales');
    }
}
