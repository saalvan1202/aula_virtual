<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsistenciasUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistencias_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_periodo_clases')
                ->constrained('periodo_clases')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_usuario')
                ->constrained('usuarios')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->date('fecha');
            $table->string('tipo_asistencia',50);//PRESENTE,FALTA,TARDE,JUSTIFICADO
            $table->string('observaciones',250)->nullable();
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
        Schema::dropIfExists('asistencias_usuarios');
    }
}
