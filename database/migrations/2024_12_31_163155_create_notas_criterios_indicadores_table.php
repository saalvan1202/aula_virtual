 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotasCriteriosIndicadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notas_criterios_indicadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_criterio_evaluacion_indicador')
                ->constrained('criterios_evaluaciones_indicadores')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_curso_matricula')
                ->constrained('cursos_matriculas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('asistio',1)->nullable();
            $table->decimal('nota',20,2)->nullable();
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
        Schema::dropIfExists('notas_criterios_indicadores');
    }
}
