<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUbigeosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        set_time_limit(0);
        Schema::create('ubigeos', function (Blueprint $table) {
            $table->id();
            $table->string('cod_dpto',10);
            $table->string('cod_prov',10);
            $table->string('cod_dist',10);
            $table->string('cod_ccpp',10);
            $table->string('nombre',120);
            $table->string('estado',1)->default('A');
            $table->timestamps();
        });
        $sql=file_get_contents(database_path().'/seeders/ubigeos.sql');
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ubigeos');
    }
}
