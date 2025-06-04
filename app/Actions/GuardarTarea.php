<?php

namespace App\Actions;

use App\Models\CursoDocenteSeccion;
use App\Models\CursoMatricula;
use App\Models\RecursoTareaEstudiante;
use App\Models\RecursoTarea;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use ZipArchive;

class GuardarTarea
{
    public function handle($data)
    {
        return DB::transaction(function () use ($data) {
            $id_curso_matricula = $data['id_curso_matricula'];
            $id_recurso_tarea = $data['id_recurso_tarea'];
            $files = $data['files_tarea'];

            $recurso_tarea = RecursoTarea::findOrFail($id_recurso_tarea);

            // Validar extensiones permitidas
            $validaciones = [
                '' => [], // Todos los archivos
                'pdf' => ['pdf'],
                'doc,pdf,docx,txt' => ['doc', 'pdf', 'docx', 'txt'],
                'xls,xlsx' => ['xls', 'xlsx']
            ];

            $tipoArchivos = $recurso_tarea->tipo_archivos;
            $extensionesPermitidas = $validaciones[$tipoArchivos] ?? [];

            foreach ($files as $file) {
                $extension = strtolower($file->getClientOriginalExtension());

                if (!empty($extensionesPermitidas) && !in_array($extension, $extensionesPermitidas)) {
                    throw ValidationException::withMessages([
                        'files_tarea' => ["El archivo '{$file->getClientOriginalName()}' no tiene una extensión permitida. Se permiten: " . implode(', ', $extensionesPermitidas)]
                    ]);
                }
            }

            // Crear registro en RecursoTareaEstudiante
            $recurso_tarea_estudiante = new RecursoTareaEstudiante();

            $recurso_tarea_estudiante->id_recurso_tarea = $id_recurso_tarea;
            $recurso_tarea_estudiante->id_curso_matricula = $id_curso_matricula;
            $recurso_tarea_estudiante->puntaje = 0;
            $recurso_tarea_estudiante->estado_calificacion = 'PENDIENTE';
            $recurso_tarea_estudiante->estado_envio = 'PRESENTADO';
            $recurso_tarea_estudiante->tipo = 'ARCHIVO';
            $recurso_tarea_estudiante->estado = 'A';
            $recurso_tarea_estudiante->save();

            // Procesar múltiples archivos y crear un ZIP si hay más de un archivo
            if (count($files) > 1) {
                $dni = auth()->user()->persona->numero_documento;
                $fechaActual = Carbon::now();
                $name_file = $dni . '_' . $fechaActual->format('YmdHis');
                $zipFilePath = $this->createZip($files, $name_file);

                // Usar CrearArchivo para guardar el archivo ZIP
                $zipFile = new \Illuminate\Http\UploadedFile(
                    $zipFilePath,
                    basename($zipFilePath)
                );
                $crearArchivo = app(CrearArchivo::class);
                $crearArchivo->setSubject($recurso_tarea_estudiante)->handle($zipFile);
            } else {
                // Si es un solo archivo, guardarlo directamente
                $crearArchivo = app(CrearArchivo::class);
                $crearArchivo->setSubject($recurso_tarea_estudiante)->handle($files[0]);
            }

            return $recurso_tarea_estudiante;
        });
    }

    private function createZip($files, $name_file)
    {
        $zipFileName = "{$name_file}.zip";
        $zipFilePath = Storage::path("temp/{$zipFileName}");

        // Crear directorio temporal si no existe
        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                if (!$file instanceof \Illuminate\Http\UploadedFile) {
                    throw new \Exception("Uno de los archivos no es válido.");
                }

                $filePath = $file->getRealPath();
                $fileName = $file->getClientOriginalName();

                // Validar que la ruta del archivo no sea nula
                if (empty($filePath)) {
                    throw new \Exception("El archivo '{$fileName}' no tiene una ruta válida. Ruta: [{$filePath}]");
                }

                try {
                    $zip->addFile($filePath, $fileName);
                } catch (\Exception $e) {
                    throw new \Exception("Error al añadir el archivo '{$fileName}' al ZIP. Ruta: [{$filePath}]. Error: {$e->getMessage()}");
                }
            }
            $zip->close();
        } else {
            throw new \Exception("No se pudo crear el archivo ZIP.");
        }

        return $zipFilePath;
    }
}
