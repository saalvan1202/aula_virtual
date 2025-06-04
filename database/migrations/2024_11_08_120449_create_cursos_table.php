<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->foreignId('id_plan_estudio')
                ->constrained('plan_estudios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_ciclo')
                ->constrained('ciclos')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('id_modulo_competencia')->index();
            $table->string('descripcion',100);
            $table->integer('horas_teoria');
            $table->integer('horas_practica');
            $table->integer('creditos_teoria');
            $table->integer('creditos_practica');
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
        Schema::dropIfExists('cursos');
    }
}
