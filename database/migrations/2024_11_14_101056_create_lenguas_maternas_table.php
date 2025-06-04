<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLenguasMaternasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lenguas_maternas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',150);
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        $lenguas=['ESPAÑOL','QUECHUA','AYMARA','ASHANINKA','AWAJÚN','BORA','SHIPIBO','SHIPIBO-KONIBO','INGLES'];
        $insert=[];
        foreach($lenguas as $lengua){
            $insert[]=[
                'descripcion'=>$lengua,
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ];
        }
        DB::table('lenguas_maternas')->insert($insert);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lenguas_maternas');
    }
}
