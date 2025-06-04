<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_curso_docente')
                ->constrained('cursos_docentes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_aula')
                ->constrained('aulas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('dia')->comment('Lunes,Martes,Miercoles,Jueves,Viernes,Sabado,Domingo');
            $table->string('tipo')->comment('TEORIA,PRACTICA');
            $table->string('hora_inicio',10);
            $table->string('hora_fin',10);
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
        Schema::dropIfExists('horarios');
    }
}
