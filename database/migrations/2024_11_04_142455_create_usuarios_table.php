<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perfil')
                ->constrained('perfiles')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_persona')
                ->constrained('personas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->integer('id_referencia');//DOCENTE,ESTUDIANTE
            $table->string('referencia',25)->nullable();
            $table->string('usuario')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('usuarios');
    }
}
