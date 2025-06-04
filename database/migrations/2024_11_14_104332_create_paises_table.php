<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePaisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        set_time_limit(0);
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 2);
            $table->string('descripcion', 120);
            $table->string('estado',1);
            $table->timestamps();
        });
        $sql=file_get_contents(database_path().'/seeders/paises.sql');
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paises');
    }
}
