<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddColumnsCursosDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursos_docentes', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->index();
            $table->string('culminado')->nullable()->default('N');
            $table->string('visible')->nullable()->default('S');
            $table->timestamp('fecha_inicio')->nullable();
        });
        $cursos = \App\Models\CursoDocente::all();
        foreach ($cursos as $curso) {
            $curso->culminado = 'N';
            $curso->visible = 'S';
            $curso->uuid=(string)Str::uuid();
            $curso->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cursos_docentes', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'culminado']);
        });
    }
}
