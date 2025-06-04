<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAulasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sede')
                ->constrained('sedes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_tipo_aula')
                ->constrained('tipo_aulas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('descripcion',100);
            $table->integer('piso')->nullable();
            $table->string('ubicacion',100)->nullable();
            $table->integer('aforo')->nullable();
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
        Schema::dropIfExists('aulas');
    }
}
