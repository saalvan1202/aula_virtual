<?php
use App\Http\Controllers\AsistenciaAlumnoController;
use App\Http\Controllers\AsistenciaEstudiantesController;
use App\Http\Controllers\AulaVirtualController;
use App\Http\Controllers\RecursoForoRespuestaController;
use App\Http\Controllers\RecursoTareaController;
use App\Http\Controllers\RecursoExamenController;
use App\Http\Controllers\RecursoTareaEstudianteController;
use App\Http\Controllers\SeccionRecursoController;
use App\Http\Controllers\ClasesDocentesController;
use App\Http\Controllers\CriteriosEvaluacieonesIndicadoresController;
use App\Http\Controllers\CursosDocentesIndicadoresController;
use App\Http\Controllers\EstudianteExamenController;
use App\Http\Controllers\EstudianteExamenRespuestaController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\EvaluacionEstudiantesController;
use App\Http\Controllers\GestionCursosController;
use App\Http\Controllers\GestionCursosEstudiantesController;
use App\Http\Controllers\PeriodoClaseController;
use App\Http\Controllers\RecursoExamenPreguntaController;
use App\Http\Controllers\RecursoForoController;
use App\Http\Controllers\RecursoPreguntaAlternativaController;

Route::prefix('gestion-cursos')->middleware('auth.permission:aula-virtual')->group(function () {
    //SILABO
    Route::post('silabo/', [GestionCursosController::class, 'saveSilabo'])->name('gestion-cursos.saveSilabo');
    Route::get('silabo/{id}', [GestionCursosController::class, 'getSilabo'])->name('gestion_cursos.getSilabo');
    Route::get('gestion_evaluaciones/{uuid}/{id}', [GestionCursosController::class, 'indexCalificaciones'])->name('calificaciones_criterios.index');
    //Al tener los mismos atributos y llamada al index, se cruzan, para ello se decalra primero
    Route::get('data-estudiante/{id}', [GestionCursosController::class, 'getEstudiante'])->name('gestion.matriculados');
    Route::get('/{uuid}', [GestionCursosController::class, 'index'])->name('gestion-cursos.index');
    Route::get('estudiantes/{uuid}', [GestionCursosEstudiantesController::class, 'index'])->name('gestion-cursos.index');
    Route::get('estudiantes/{tipo_actividad}/{uuid}', [GestionCursosEstudiantesController::class, 'listaActividadesTipo'])->name('gestion-cursos.listaActividadesTipo');

    Route::get('matriculados/{uuid}', [GestionCursosController::class, 'indexEstudiantes'])->name('gestion-cursos.index');
    Route::get('indicadores/{uuid}', [GestionCursosController::class, 'indexIndicadores'])->name('gestion-cursos.index');
    //CLASES
    Route::get('clases/{uuid}', [GestionCursosController::class, 'indexClases'])->name('gestion-cursos.index');
    //ASISTENCIA ALUMNOS
    Route::get('asistencia-alumnos/{uuid}', [GestionCursosController::class, 'indexAsistencia'])->name('gestion-cursos.indexAsistencia');
    Route::get('curso-docente/{uuid}', [EvaluacionController::class, 'cursoDocente'])->name('periodo_clases.index');
    Route::post('/', [EvaluacionController::class, 'store'])->name('periodo_clases.store');
    Route::get('/{id}', [PeriodoClaseController::class, 'destroy'])->name('periodo_clases.destroy');
    Route::get('/{periodo}', [EvaluacionController::class, 'getCursos'])->name('periodo_clases.destroy');
    //AULA VIRTUAL
    Route::get('virtual-classroom/{id_curso}/{id_periodo_clases}', [AulaVirtualController::class, 'getSeccionCursosDocente'])->name('virtualclassroom.getSeccionCursosDocente');
    Route::get('virtual-classroom/{uuid}', [AulaVirtualController::class, 'index'])->name('virtualclassroom.index');
    Route::post('virtual-classroom/store', [AulaVirtualController::class, 'store'])->name('virtualclassroom.store');
    Route::put('virtual-classroom/update/{id}', [AulaVirtualController::class, 'update'])->name('virtualclassroom.update');
    Route::delete('virtual-classroom/{id}', [AulaVirtualController::class, 'destroy'])->name('virtualclassroom.destroy');
    //RECURSOS
    Route::get('virtual-classroom/import-recursos/{id_seccion}/{tipo_actividad}', [SeccionRecursoController::class, 'importActividad'])->name('recurso.importActividad');
    Route::post('virtual-classroom/recursos/store', [SeccionRecursoController::class, 'store'])->name('recurso.store');
    Route::delete('virtual-classroom/recursos/destroy/{id}', [SeccionRecursoController::class, 'destroy'])->name('recurso.destroy');
    Route::put('virtual-classroom/recursos/update/{id}', [SeccionRecursoController::class, 'update'])->name('recurso.update');
    // TAREAS
    Route::get('virtual-classroom/{uuid}/recursos-tareas/{id}', [RecursoTareaController::class, 'index'])->name('recurso-tarea.index');
    Route::post('virtual-classroom/recursos-tareas/store', [RecursoTareaController::class, 'store'])->name('recurso-tarea.store');
    //CULMINAR CURSO
    Route::post('culminar-curso/{uuid}', [GestionCursosController::class, 'culminarCurso'])->name('gestion-cursos.culminarCurso');
    Route::post('virtual-classroom/recursos-tareas/update/{id}', [RecursoTareaController::class, 'update'])->name('recurso-tarea.update');
    Route::post('virtual-classroom/recursos-tareas-estudiante/store', [RecursoTareaEstudianteController::class, 'store'])->name('recurso-tarea-estudiante.store');
    Route::post('virtual-classroom/recursos-tareas-estudiante/update-notas', [RecursoTareaEstudianteController::class, 'updateNotas'])->name('recurso-tarea-estudiante.update-notas');
    Route::delete('virtual-classroom/recursos-tareas-estudiante/destroy/{id}', [RecursoTareaEstudianteController::class, 'destroy'])->name('recurso-tarea-estudiante.destroy');
    //FOROS
    Route::get('virtual-classroom/{uuid}/foros/{id}', [RecursoForoController::class, 'index'])->name('foro.index');
    Route::get('virtual-classroom/{uuid}/foros/{id}/calificar', [RecursoForoController::class, 'calificar'])->name('foro.calificar');
    Route::post('virtual-classroom/recursos-foros/store', [RecursoForoController::class, 'store'])->name('recurso-foro.store');
    Route::put('virtual-classroom/recursos-foros/update/{id}', [RecursoForoController::class, 'update'])->name('recurso-foro.update');
    Route::post('virtual-classroom/recursos-foros-respuestas/store', [RecursoForoRespuestaController::class, 'store'])->name('recurso-foro-respuesta.store');
    Route::post('virtual-classroom/recursos-foros-respuestas/update-notas', [RecursoForoRespuestaController::class, 'updateNotas'])->name('recurso-foro-respuesta.update-notas');
    Route::delete('virtual-classroom/recursos-foros-respuestas/destroy/{id}', [RecursoForoRespuestaController::class, 'destroy'])->name('recurso-foro-respuesta.destroy');
    //EXAMENES
    Route::get('virtual-classroom/{uuid}/recursos-examenes/{id}', [RecursoExamenController::class, 'index'])->name('recurso-examen.index');
    Route::get('virtual-classroom/{uuid}/recursos-examenes/{id}/calificar', [RecursoExamenController::class, 'calificar'])->name('recurso-examen.calificar');
    Route::get('virtual-classroom/{uuid}/recursos-examenes/{id}/calificar/{id_curso_matricula}', [RecursoExamenController::class, 'calificarManualmente'])->name('recurso-examen.calificar-manualmente');
    Route::get('virtual-classroom/{uuid}/resolver-examenes/{id}', [RecursoExamenController::class, 'solveExam'])->name('recurso-examen.solve-exam');
    Route::post('virtual-classroom/recursos-examenes/store', [RecursoExamenController::class, 'store'])->name('recurso-examen.store');
    Route::put('virtual-classroom/recursos-examenes/update/{id}', [RecursoExamenController::class, 'update'])->name('recurso-examen.update');
    Route::post('virtual-classroom/recursos-examenes/disable-exam/{id_seccion_recurso}', [RecursoExamenController::class, 'disableExam'])->name('recurso-examen.disable-exam');
    Route::post('virtual-classroom/recursos-examenes/enable-exam/{id_recurso_examen}', [RecursoExamenController::class, 'enableExam'])->name('recurso-examen.enable-exam');
    //PREGUNTAS
    Route::post('virtual-classroom/recursos-examenes-preguntas/store', [RecursoExamenPreguntaController::class, 'store'])->name('recurso-examen-pregunta.store');
    //IMPORT RECURSOS
    Route::post('virtual-classroom/import-recursos/store', [SeccionRecursoController::class, 'storeMigrateActividades'])->name('storeMigrateActividades.store');


    Route::post('virtual-classroom/recursos-examenes-preguntas/update-puntajes', [RecursoExamenPreguntaController::class, 'updatePuntajes'])->name('recurso-examen-pregunta.update-puntajes');
    Route::delete('virtual-classroom/recursos-examenes-preguntas/destroy/{id}', [RecursoExamenPreguntaController::class, 'destroy'])->name('recurso-examen-pregunta.destroy');
    Route::post('virtual-classroom/recursos-examenes-preguntas/update-question', [RecursoExamenPreguntaController::class, 'updateQuestion'])->name('recurso-examen-pregunta.update-question');
    //ALTERNATIVAS
    Route::post('virtual-classroom/recursos-preguntas-alternativas/store/{id_pregunta}', [RecursoPreguntaAlternativaController::class, 'store'])->name('recurso-pregunta-alternativa.store');
    Route::delete('virtual-classroom/recursos-preguntas-alternativas/destroy/{id_pregunta}/alternativa/{id_alternativa}', [RecursoPreguntaAlternativaController::class, 'destroy'])->name('recurso-pregunta-alternativa.destroy');
    Route::put('virtual-classroom/recursos-preguntas-alternativas/update-check/{id_pregunta}', [RecursoPreguntaAlternativaController::class, 'updateCheck'])->name('recurso-pregunta-alternativa.update-check');
    Route::post('virtual-classroom/recursos-preguntas-alternativas/update-porcentajes/{id_pregunta}', [RecursoPreguntaAlternativaController::class, 'updatePorcentajes'])->name('recurso-pregunta-alternativa.update-porcentaje');
    Route::post('virtual-classroom/recursos-preguntas-alternativas/update-descripcion/{id_pregunta}', [RecursoPreguntaAlternativaController::class, 'updateDescripcion'])->name('recurso-pregunta-alternativa.update-descripcion');
    Route::post('virtual-classroom/recursos-preguntas-alternativas/change-correct', [RecursoPreguntaAlternativaController::class, 'changeCorrect'])->name('recurso-pregunta-alternativa.change-correct');
    //EXAMENES ESTUDIANTES
    Route::post('virtual-classroom/estudiantes-examenes/store', [EstudianteExamenController::class, 'store'])->name('estudiante-examen.store');
    Route::post('virtual-classroom/estudiantes-examenes-respuestas/store', [EstudianteExamenRespuestaController::class, 'store'])->name('estudiante-examen-respuesta.store');
    Route::post('virtual-classroom/estudiantes-examenes-respuestas/update-score-text', [EstudianteExamenRespuestaController::class, 'updateScoreText'])->name('estudiante-examen-respuesta.update-score-text');
});
//CLASES
Route::prefix('clases')->middleware('auth.permission:aula-virtual')->group(function () {
    Route::delete('/{id}', [ClasesDocentesController::class, 'destroy'])->name('periodo_clases.destroy');
    //LLAMADO A LA BASE DE DATOS PARA RECUPERAR LAS CLASES
    Route::post('/', [ClasesDocentesController::class, 'store'])->name('clases_docentes.store');
    Route::get('/{id}', [ClasesDocentesController::class, 'getClases'])->name('clases_docentes.getClases');
    Route::get('clases/{uuid}', [GestionCursosController::class, 'indexClases'])->name('gestion-cursos.index');;
    Route::get('{id}/edit', [ClasesDocentesController::class, 'edit'])->name('periodo_clases.edit');
});
//ASISTENCIA
Route::prefix('asistencia')->middleware('auth.permission:aula-virtual')->group(function () {
    Route::get('get-estudiantes/', [AsistenciaAlumnoController::class, 'getEstudiantes'])->name('asistencia_alumnos.getEstudiantes');
    Route::post('/', [AsistenciaAlumnoController::class, 'store'])->name('asistencia_alumnos.store');


});
//INDICADORES
Route::prefix('indicador-docente')->middleware('auth.permission:aula-virtual')->group(function (){
    Route::post('/',[CursosDocentesIndicadoresController::class, 'store'])->name('cursos_docentes_indicadores.store');
    Route::get('/{uuid}',[CursosDocentesIndicadoresController::class, 'getIndicadores'])->name('cursos_docentes_indicadores.getIndicadores');
    Route::get('{id}/edit',[CursosDocentesIndicadoresController::class, 'edit'])->name('cursos_docentes_indicadores.edit');
    Route::delete('/{id}',[CursosDocentesIndicadoresController::class, 'destroy'])->name('cursos_docentes_indicadores.destroy');
});
//CRITERIOS EVALUACIÓN
Route::prefix('criterios-evaluacion')->middleware('auth.permission:aula-virtual')->group(function (){
    Route::post('/',[CriteriosEvaluacieonesIndicadoresController::class, 'store'])->name('criterios_evaluacion.store');
    Route::get('/{id}',[CriteriosEvaluacieonesIndicadoresController::class, 'getCriteriosEvaluaciones'])->name('cursos_docentes_indicadores.getCriteriosEvaluaciones');
    Route::get('{id}/edit',[CriteriosEvaluacieonesIndicadoresController::class, 'edit'])->name('criterios_evaluacion.edit');
    Route::delete('/{id}',[CriteriosEvaluacieonesIndicadoresController::class, 'destroy'])->name('criterios_evaluacion.destroy');
});
//CALIFICACIONES CRITERIOS DE EVALUACION
Route::prefix('calificaciones')->middleware('auth.permission:aula-virtual')->group(function (){
    //ESTUDIANTES
    //ESTE PRIMER INDEX CONTIENE LA GESTIÓN DE NOTAS DE LOS ESTUDIANTES Y LA VISUALIZACIÓN DE LAS NOTAS DE CASA ESTUDAIANTES PARA EL PERFIL ESTUDIANTE
    Route::get('/',[EvaluacionEstudiantesController::class, 'indexNotasEstudiantes'])->name('evaluacion_estudiantes.indexNotasEstudiantes');
    Route::get('get-estudiantes/',[CriteriosEvaluacieonesIndicadoresController::class, 'getEstudiantes'])->name('evaluacion_estudiantes.getEstudiantes');
    Route::get('/{uuid}/{id}',[EvaluacionEstudiantesController::class, 'indexCalificarCurso'])->name('evaluacion_estudiantes.indexCalificarCurso');
    Route::get('estudiantes/notas/{id}/{id_matricula}',[EvaluacionEstudiantesController::class, 'getNotasCursos'])->name('evaluacion_estudiantes.getNotasCursos');
    Route::post('subir-notas/',[CriteriosEvaluacieonesIndicadoresController::class, 'subirNotas'])->name('criterios_evaluacion.subirNotas');
    Route::post('detalle-notas/',[CriteriosEvaluacieonesIndicadoresController::class, 'detalleActiviadadesAlumnos'])->name('criterios_evaluacion.detalleActiviadadesAlumnos');
    Route::post('/',[CriteriosEvaluacieonesIndicadoresController::class, 'store'])->name('criterios_evaluacion.store');
    Route::get('/{id}',[CriteriosEvaluacieonesIndicadoresController::class, 'getCriteriosEvaluaciones'])->name('cursos_docentes_indicadores.getCriteriosEvaluaciones');
    Route::get('{id}/edit',[CriteriosEvaluacieonesIndicadoresController::class, 'edit'])->name('criterios_evaluacion.edit');
    Route::post('pre-visualizar/',[CriteriosEvaluacieonesIndicadoresController::class, 'calificarCriteriosEvaluacion'])->name('criterios_evaluacion.calificarCriteriosEvaluacion');
    Route::delete('/{id}',[CriteriosEvaluacieonesIndicadoresController::class, 'destroy'])->name('criterios_evaluacion.destroy');

});
//ASISTENCIA
Route::prefix('asistencia')->middleware('auth.permission:aula-virtual')->group(function (){
    Route::get('/',[AsistenciaEstudiantesController::class, 'indexAsistenciasEstudiantes'])->name('asistencia_estudiantes.indexAsistenciasEstudiantes');
    Route::get('/{id}',[AsistenciaEstudiantesController::class, 'getAsistenciaAlumnos'])->name('asistencia_estudiantes.getAsistenciaAlumnos');
    Route::get('detalle/{id_curso_docente}/{id_curso_matricula}',[AsistenciaEstudiantesController::class, 'getDetalleAsistenciaAlumno'])->name('asistencia_estudiantes.getDetalleAsistenciaAlumno');
});
//HORARIO
Route::prefix('horario')->middleware('auth.permission:aula-virtual')->group(function (){
    Route::get('/',[GestionCursosController::class, 'indexHorario'])->name('horario-alumnos.indexHorario');
});
//CALIFICAR
Route::prefix('promedios')->middleware('auth.permission:aula-virtual')->group(function (){
    Route::get('{uuid}/',[EvaluacionController::class, 'getPromedios'])->name('horario-alumnos.indexHorario');
    Route::post('inhabilitar/{id_curso_matricula}/',[EvaluacionController::class, 'inhabilitarEstudiante'])->name('horario-alumnos.inhabilitarEstudiante');
    Route::post('subir-notas/',[EvaluacionController::class, 'subirNotas'])->name('horario-alumnos.subirNotas');
    Route::get('resumen/{id_curso_docente}',[EvaluacionController::class, 'getResumen'])->name('horario-alumnos.getResumen');
    Route::get('calficar-criterios/{uuid}',[EvaluacionController::class, 'getCalificar'])->name('horario-alumnos.getCalificar');
});
