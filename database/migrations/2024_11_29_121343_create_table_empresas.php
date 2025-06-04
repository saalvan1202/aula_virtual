<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEmpresas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('ruc',11)->nullable();
            $table->string('razon_social',300)->nullable();
            $table->string('nombre_comercial',150)->nullable();
            $table->string('rubro',150)->nullable();
            $table->integer('id_ubigeo')->index()->default(0);
            $table->string('direccion',300)->nullable();
            $table->string('telefono',50)->nullable();
            $table->string('representante_legal',150)->nullable();
            $table->string('email',150)->nullable();
            $table->string('pagina_web',150)->nullable();
            $table->text('descripcion')->nullable();
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
        Schema::dropIfExists('empresas');
    }
}
