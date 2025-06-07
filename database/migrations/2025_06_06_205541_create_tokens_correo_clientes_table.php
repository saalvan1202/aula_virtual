<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokensCorreoClientesTable extends Migration
{
    public function up()
    {
        Schema::create('tokens_correo_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tokens_correo_clientess');
    }
}
