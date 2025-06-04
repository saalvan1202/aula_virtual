<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcesosAdmisionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procesos_admision', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',100);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
           // $table->integer('vacantes')->nullable();
            $table->decimal('puntaje_minimo',10)->nullable();
            $table->string('vigente',1)->nullable()->comment('S,N');
            $table->string('estado',1);
            $table->timestamps();
        });
        Schema::create('modalidad_procesos_admision', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_modalidad_admision')
                ->constrained('modalidades_admision')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_proceso_admision')
                ->constrained('procesos_admision')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->date('fecha_examen')->nullable();
            $table->time('hora_examen')->nullable();
            $table->integer('vacantes')->nullable();//solo cuando es EXONERADO
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
        Schema::dropIfExists('procesos_admision');
    }
}
