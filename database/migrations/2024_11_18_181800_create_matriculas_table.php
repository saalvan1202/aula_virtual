<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMatriculasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tipo_proceso_matricula')
                ->constrained('tipo_procesos_matriculas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('id_tipo_matricula')->index();
            $table->foreignId('id_estudiante')
                ->constrained('estudiantes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_periodo_clases')
                ->constrained('periodo_clases')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_turno')
                ->constrained('turnos')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_seccion')
                ->constrained('secciones')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('id_ciclo')->index();
            $table->date('fecha_matricula')->nullable();
            $table->bigInteger('id_usuario')->index();
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        DB::table('tipo_matriculas')->insert([
            [
                'descripcion'=>'REGULAR',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion'=>'EXTEMPORÁNEA',
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
        Schema::dropIfExists('matriculas');
    }
}
