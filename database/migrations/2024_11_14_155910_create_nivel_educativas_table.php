<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateNivelEducativasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nivel_educativas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',100);
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        DB::table('nivel_educativas')->insert([

            [
                'descripcion' => 'INICIAL',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion' => 'PRIMARIA',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion' => 'SECUNDARIA',
                'estado' => 'A',
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
        Schema::dropIfExists('nivel_educativas');
    }
}
