<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTipoContratosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_contratos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        DB::table('tipo_contratos')->insert([
            [
                'id'=>0,
                'descripcion'=>'NINGUNA',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ]
        ]);
        DB::table('tipo_contratos')->insert([
            [
                'descripcion'=>'CONTRATADO',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion'=>'NOMBRADO',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_contratos');
    }
}
