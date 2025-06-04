<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->string('sexo',5)->default('M')->nullable();
            $table->string('estado_civil',20)->default('SOLTERO')->nullable();
            $table->date('fecha_nacimiento')->nullable();
        });
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('es_usuario',1)->default('S');
            $table->integer('id_tipo_contrato')->default(0);
            $table->string('email',100)->nullable();
            $table->string('telefono',20)->nullable();
            $table->string('profesion',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn(['sexo','estado_civil','fecha_nacimiento']);
        });
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['id_tipo_contrato','email','telefono']);
        });
    }
}
