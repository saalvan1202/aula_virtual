<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BolsasProgramaEstudios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolsas_programa_estudios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bolsa_laboral')
                ->constrained('bolsas_laborales')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_programa_estudio')
                ->constrained('programa_estudios')
                ->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('bolsas_programa_estudios');
    }
}
