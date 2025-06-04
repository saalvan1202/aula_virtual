<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModalidadesAdmisionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modalidades_admision', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->comment('ORDINARIO,EXONERADO,EXTRAORDINARIO');
            $table->string('descripcion',100);
            $table->string('estado',1);
            $table->timestamps();
        });
        Schema::create('modalidad_requisitos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_modalidades_admision')
                ->constrained('modalidades_admision')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('descripcion',100);
            $table->string('obligatorio',1);
            $table->string('estado',1);
            $table->timestamps();
        });

        DB::table('modalidades_admision')->insert([
            [
                'tipo'=>'ORDINARIO',
                'descripcion'=>'ORDINARIO',
                'estado'=>'A',
                'created_at'=>now(),
                'updated_at'=>now()
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
        Schema::dropIfExists('modalidades_admision');
    }
}
