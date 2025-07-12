<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispositivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45); // Soporta IPv6 (::1)
            $table->string('so', 50); // Sistema operativo
            $table->string('version_so', 20)->nullable();
            $table->string('dispositivo', 100)->nullable();
            $table->boolean('es_movil')->default(false);
            $table->boolean('es_escritorio')->default(false);
            $table->boolean('es_tablet')->default(false);
            $table->string('estado')->default('A');
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
        Schema::dropIfExists('dispositivos');
    }
}
