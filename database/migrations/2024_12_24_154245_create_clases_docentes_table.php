<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClasesDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clases_docentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_curso_docente')
                ->constrained('cursos_docentes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->integer('numero');
            $table->string('descripcion',150)->nullable();
            $table->date('fecha')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('estado_clase',20)->nullable()->default('PENDIENTE');//PENDIENTE, DICTADA
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
        Schema::dropIfExists('clases_docentes');
    }
}
