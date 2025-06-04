<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_referencia')->index();
            $table->string('referencia',100)->index();
            $table->bigInteger('id_detalle')->index();
            $table->string('codigo',50)->nullable();
            $table->string('nombre',300);
            $table->string('archivo',300);
            $table->unsignedBigInteger('bytes');
            $table->text('observaciones')->nullable();
            $table->string('verificado',1)->default('N');
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
        Schema::dropIfExists('archivos');
    }
}
