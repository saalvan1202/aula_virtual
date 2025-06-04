<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInstitutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('institutos', function (Blueprint $table) {
            $table->integer('id_tipo_gestion_educativa')->default(1);
            $table->string('email',150)->nullable();
            $table->string('departamento',120)->nullable();
            $table->string('provincia',120)->nullable();
            $table->string('distrito',120)->nullable();
            $table->integer('id_ubigeo')->default(0);
            $table->string('director_general',150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('institutos', function (Blueprint $table) {
            $table->dropColumn(['id_tipo_gestion_educativa','email','director_general']);
        });
    }
}
