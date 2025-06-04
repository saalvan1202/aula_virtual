<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTiposPreguntasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipos_preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo',15)->index();
            $table->string('nombre',80);
            $table->string('descripcion',200);
            $table->string('icono',20);
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        DB::table('tipos_preguntas')->insert([
           [
               'codigo'=>'SM',
               'nombre'=>'Seleccion Simple',
               'descripcion'=>'Permite la selección de una  respuesta a partir de una lista predefinida.',
              // 'icono'=>'CheckCircleIcon',
               'icono'=>'ListIcon',
               'estado'=>'A',
               'created_at'=>$now,
               'updated_at'=>$now
           ],
            [
                'codigo'=>'OM',
                'nombre'=>'Opcion Multiple',
                'descripcion'=>'Permite la selección de una o varias respuestas a partir de una lista predefinida.',
                'icono'=>'CheckSquareIcon',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'codigo'=>'VF',
                'nombre'=>'Verdadero/Falso',
                'descripcion'=>'Forma simple de pregunta de opción múltiple con dos únicas posibilidades ("Verdadero" y "Falso").',
                'icono'=>'PauseIcon',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'codigo'=>'T',
                'nombre'=>'Texto',
                'descripcion'=>'Permite una respuesta de  texto en línea',
                'icono'=>'FileTextIcon',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipos_preguntas');
    }
}
