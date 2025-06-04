<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulosCompetenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulos_competencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_plan_estudio')
                ->constrained('plan_estudios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_componente')
                ->constrained('componentes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->integer('id_padre')->index();
            $table->string('codigo',10)->nullable();
            $table->string('descripcion',400);
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
        Schema::dropIfExists('modulos_competencias');
    }
}
