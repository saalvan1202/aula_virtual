<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tipo_documento_identidad')
                ->constrained('tipo_documentos_identidades')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('nombres',150);
            $table->string('apellido_paterno',150);
            $table->string('apellido_materno',150);
            $table->string('numero_documento',150);
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
        Schema::dropIfExists('personas');
    }
}
