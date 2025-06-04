<?php

namespace App\Http\Controllers;

use App\Actions\GuardarTarea;
use App\Models\RecursoTarea;
use App\Models\RecursoTareaEstudiante;
use App\Services\ValidacionArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Carbon\Carbon;

class RecursoTareaEstudianteController extends Controller
{
    public function store(Request $request, GuardarTarea $guardarTarea)
    {
        // Obtener el recurso tarea
        $recursoTarea = RecursoTarea::findOrFail($request->id_recurso_tarea);

        // Validar fecha actual con fecha_inicio y fecha_fin
        $fechaActual = Carbon::now();
        $fechaInicio = Carbon::parse($recursoTarea->fecha_inicio);
        $fechaFin = Carbon::parse($recursoTarea->fecha_fin);

        if ($fechaActual->lt($fechaInicio)) {
            return response()->json([
                'message' => "No puedes subir archivos aún. La tarea comienza el {$fechaInicio->format('d-m-Y H:i')}.",
            ], 409);
        }

        if ($fechaActual->gt($fechaFin)) {
            return response()->json([
                'message' => "No puedes subir archivos. La tarea finalizó el {$fechaFin->format('d-m-Y H:i')}.",
            ], 409);
        }

        // Verificar si la tarea ya ha sido calificada
        $tareaCalificada = RecursoTareaEstudiante::where('id_recurso_tarea', $request->id_recurso_tarea)
            ->where('id_curso_matricula', $request->id_curso_matricula)
            ->where('estado_calificacion', 'CALIFICADO')
            ->where('estado', 'A')
            ->exists();

        if ($tareaCalificada) {
            return response()->json([
                'message' => "Tu tarea ya ha sido calificada.",
            ], 409);
        }

        // Verificar intentos disponibles
        $totalIntentosUsados = RecursoTareaEstudiante::where('id_recurso_tarea', $request->id_recurso_tarea)->where('id_curso_matricula', $request->id_curso_matricula)->where('estado','A')->count();
        $intentosDisponibles = $recursoTarea->numero_intento - $totalIntentosUsados;

        if ($intentosDisponibles <= 0) {
            return response()->json([
                'message' => "Ya no quedan intentos disponibles para esta tarea.",
            ], 409);
        }

        // Obtener el tipo de archivos permitidos según el recurso tarea
        $validaciones = [
            '' => 'pdf,doc,pdf,docx,txt,xls,xlsx', // Todos los archivos permitidos
            'pdf' => 'pdf',
            'doc,pdf,docx,txt' => 'doc,pdf,docx,txt',
            'xls,xlsx' => 'xls,xlsx',
        ];

        $tipoArchivos = $recursoTarea->tipo_archivos;
        $extensionesPermitidas = $validaciones[$tipoArchivos] ?? '*';
        $validator=Validator::make($request->all(),[
            'files_tarea' => 'required|array',
            'id_curso_matricula' => 'required|integer',
            'id_recurso_tarea' => 'required|integer',
        ]);
        $validator->after(function ($validator) use ($request,$extensionesPermitidas) {
            $valid=new ValidacionArchivo();
            $valid->setExtension(explode(',',$extensionesPermitidas));
            $files=$request->file('files_tarea');
            foreach($files as $key=>$file) {
                $error=$valid->validate($file)->getError();
                if($error){
                    $validator->errors()->add('files_tarea.'.$key, $error);
                }
            }
        });
        $validatedData=$validator->validate();
        /*try {
            $validatedData = $request->validate([
                'files_tarea' => 'required|array',
                'files_tarea.*' => "file|max:8192|mimes:$extensionesPermitidas",
                'id_curso_matricula' => 'required|integer',
                'id_recurso_tarea' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();

            // Personalizar los errores para files_tarea.*
            if ($errors->has('files_tarea.*')) {
                $files = $request->file('files_tarea');
                foreach ($files as $index => $file) {
                    $originalName = $file->getClientOriginalName();

                    // Verificar si el archivo supera el tamaño máximo
                    if ($file->getSize() > 8192 * 1024) {
                        $errors->add("files_tarea.$index", "El archivo '$originalName' excede el tamaño máximo permitido de 8MB.");
                    } else {
                        // Error de tipo de archivo
                        $errors->add("files_tarea.$index", "El archivo '$originalName' debe ser un archivo de tipo: $extensionesPermitidas.");
                    }
                }
            }

            throw $e; // Re-lanzar con mensajes personalizados
        }*/

        $response = $guardarTarea->handle($validatedData);

        return response()->json($response);
    }

    public function updateNotas(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'calificaciones' => 'required|array',
                'calificaciones.*.id' => 'required|integer|exists:recursos_tareas_estudiantes,id',
                'calificaciones.*.estado_calificacion' => 'required|string',
                'calificaciones.*.puntaje' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|between:0.00,20.00',
            ]);

            DB::beginTransaction();

            foreach ($validatedData['calificaciones'] as $calificacion) {
                $updateData = [
                    'estado_calificacion' => $calificacion['estado_calificacion'],
                    'puntaje' => $calificacion['puntaje'],
                    'fecha_calificacion' => Carbon::now(),
                ];

                if ($calificacion['estado_calificacion'] === 'PENDIENTE') {
                    $updateData['puntaje'] = '0.00';
                    $updateData['fecha_calificacion'] = null;
                }

                RecursoTareaEstudiante::where('id', $calificacion['id'])
                    ->update($updateData);
            }

            DB::commit();

            return response()->json(['message' => 'Calificaciones actualizadas con éxito.']);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Hubo un error al actualizar las calificaciones.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $recursoTareaEstudiante = RecursoTareaEstudiante::findOrFail($id);
            $recursoTareaEstudiante->estado_calificacion = 'PENDIENTE';
            $recursoTareaEstudiante->puntaje = '0.00';
            $recursoTareaEstudiante->fecha_calificacion = null;
            $recursoTareaEstudiante->estado = 'I';
            $recursoTareaEstudiante->save();

            return response()->json(['message' => 'Tarea eliminada con exito.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar la tarea.'], 500);
        }
    }
}
