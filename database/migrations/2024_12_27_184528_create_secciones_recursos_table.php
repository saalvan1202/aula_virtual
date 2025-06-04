<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeccionesRecursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secciones_recursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_curso_docente_seccion')
                ->constrained('cursos_docentes_secciones')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_tipo_recurso')
                ->constrained('tipos_recursos')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('nombre',150);
            $table->text('descripcion')->nullable();
            $table->string('tipo',10)->nullable();//ARCHIVO,URL
            $table->text('url')->nullable();
            $table->string('mostrar',1);
            $table->string('completado',1)->nullable();
            $table->string('estado',1)->nullable();
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
        Schema::dropIfExists('secciones_recursos');
    }
}
