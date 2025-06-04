<?php

namespace App\Services;

use App\Models\Ciclo;
use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\ProgramaEstudio;
use App\Models\TipoGestionEducativa;
use App\Models\Ubigeo;
use Illuminate\Support\Facades\DB;

class EstudianteService {

    public function processImportFile($reader, $id_sede, $columnMap, $guardarPersona, $guardarUsuario)
    {
        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }
            break;
        }

        if (count($rows) < 4) {
            throw new \Exception("El archivo debe tener al menos 4 filas (encabezados + datos)");
        }

        $normalizedHeaders = array_map('mb_strtoupper', array_map('trim', $rows[2]));
        $columnIndexes = $this->validateAndMapColumns($columnMap, $normalizedHeaders);

        $importedCount = 0;
        $errors = [];

        for ($i = 3; $i < count($rows); $i++) {
            try {
                $row = $rows[$i];
                $rowData = $this->extractAndValidateRowData($row, $columnIndexes, array_values($columnMap));

                $rowData = array_map(function($value) {
                    return is_string($value) ? $this->normalizeString($value) : $value;
                }, $rowData);

                // Verificar documento existente primero
                if (Persona::where('numero_documento', $rowData['numero_documento'])->exists()) {
                    $errors[] = "Fila {$i}: Documento {$rowData['numero_documento']} ya existe";
                    continue;
                }

                // Obtener relaciones con manejo de errores
                $programa_estudio = ProgramaEstudio::where('descripcion', $rowData['programa_estudios'])->first();
                if (!$programa_estudio) {
                    throw new \Exception("Programa de estudios no encontrado: '{$rowData['programa_estudios']}'");
                }

                $plan_estudio = $programa_estudio->planEstudio()->first();
                if (!$plan_estudio) {
                    throw new \Exception("No se encontró plan de estudio para el programa");
                }

                $modalidad_educativa = DB::table('modalidades_educativas')
                    ->where('abreviatura', $rowData['modalidad_educativa'])
                    ->first();
                if (!$modalidad_educativa) {
                    throw new \Exception("Modalidad educativa no encontrada: '{$rowData['modalidad_educativa']}'");
                }

                $tipo_gestion = TipoGestionEducativa::where('descripcion', $rowData['tipo_gestion'])->first();
                if (!$tipo_gestion) {
                    throw new \Exception("Tipo de gestión no encontrado: '{$rowData['tipo_gestion']}'");
                }

                $ubigeo = Ubigeo::getFullUbigeoForCode($rowData['ubigeo_ie']);
                if (!$ubigeo) {
                    throw new \Exception("Ubigeo no encontrado: '{$rowData['ubigeo_ie']}'");
                }

                $ciclo = Ciclo::where('descripcion', $rowData['ciclo'])->first();
                if (!$ciclo) {
                    throw new \Exception("Ciclo no encontrado: '{$rowData['ciclo']}'");
                }

                // Crear request con todos los campos requeridos
                $fakeRequest = new \Illuminate\Http\Request();
                $fakeRequest->replace([
                    'id_tipo_documento_identidad' => 1, // Valor fijo asumido
                    'nombres' => $rowData['nombres'],
                    'apellido_paterno' => $rowData['apellido_paterno'],
                    'apellido_materno' => $rowData['apellido_materno'],
                    'numero_documento' => $rowData['numero_documento'],
                    'estado' => 'A',
                    'estado_civil' => 'SOLTERO',
                    'sexo' => $rowData['sexo'] == 'MASCULINO' ? 'M' : 'F',
                    'fecha_nacimiento' => $this->formatDate($rowData['fecha_nacimiento']),
                    'id_perfil' => 5,
                    'id_referencia' => 0,
                    'referencia' => 'usuarios',
                    'usuario' => $rowData['numero_documento'],
                    'password' => $rowData['numero_documento'],
                    'es_usuario' => 'S',
                    'id_tipo_contrato' => 0,
                    'email' => $rowData['email'],
                    'telefono' => $rowData['telefono'],
                    'celular' => $rowData['telefono'],
                    'id_sede' => $id_sede,
                    'id_programa_estudio' => $programa_estudio->id,
                    'id_plan_estudio' => $plan_estudio->id,
                    'id_tipo_estudiante' => $rowData['ciclo'] == 'I' ? 3 : 1,
                    'id_lengua_materna' => 1,
                    'id_pais' => 174,
                    'id_modalidad_educativa' => $modalidad_educativa->id,
                    'id_nivel_educativa' => 3,
                    'id_tipo_gestion_educativa' => $tipo_gestion->id,
                    'id_ubigeo' => $ubigeo->id,
                    'id_ubigeo_institucion' => $ubigeo->id,
                    'codigo_modular' => $rowData['codigo_modular'],
                    'institucion' => $rowData['nombre_ie'],
                    'anio_egreso_institucion' => $rowData['anio_egreso'],
                    'id_ciclo' => $ciclo->id,
                    'estado_matricula' => $rowData['estado_matricula'],
                ]);

                // Guardar persona y estudiante
                $person = $guardarPersona->handle($fakeRequest);
                $user = $guardarUsuario->setKeyId('id_usuario')
                    ->isStudent()
                    ->handle($person, $fakeRequest);
                
                $estudiante = new Estudiante();
                $estudiante->id_usuario = $user->id;
                $estudiante->fill($fakeRequest->all());
                $estudiante->discapacidad = 'N';
                $estudiante->menor_edad = 'N';
                $estudiante->id_periodo_ingreso = 0;
                $estudiante->estado = 'A';
                $estudiante->save();

                $importedCount++;

            } catch (\Exception $e) {
                $errors[] = "Fila {$i}: " . $e->getMessage();
                continue;
            }
        }

        return [
            'importedCount' => $importedCount,
            'totalRows' => count($rows) - 3,
            'errors' => $errors
        ];
    }

    private function formatDate($date)
    {
        if (empty($date)) return null;
        
        try {
            $new_date = \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            return $new_date;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function validateAndMapColumns($columnMap, $normalizedHeaders)
    {
        $columnIndexes = [];
        $missingColumns = [];

        foreach ($columnMap as $excelHeader => $dbField) {
            $normalizedHeader = mb_strtoupper(trim($excelHeader));
            $colIndex = array_search($normalizedHeader, $normalizedHeaders);
            
            if ($colIndex === false) {
                $missingColumns[] = $excelHeader;
                continue;
            }
            $columnIndexes[$dbField] = $colIndex;
        }

        if (!empty($missingColumns)) {
            throw new \Exception("Las siguientes columnas son obligatorias y no se encontraron: " . implode(', ', $missingColumns));
        }

        return $columnIndexes;
    }

    private function extractAndValidateRowData($row, $columnIndexes, $requiredFields)
    {
        $rowData = [];
        $missingFields = [];

        // Extraer datos según los índices de columnas
        foreach ($columnIndexes as $dbField => $colIndex) {
            $value = isset($row[$colIndex]) ? trim($row[$colIndex]) : null;
            $rowData[$dbField] = $value;

            // Verificar campos requeridos
            if (in_array($dbField, $requiredFields) && empty($value)) {
                $missingFields[] = $dbField;
            }
        }

        if (!empty($missingFields)) {
            throw new \Exception("Los siguientes campos son obligatorios: " . implode(', ', $missingFields));
        }

        return $rowData;
    }
    
    private function normalizeString($string)
    {
        $string = mb_strtoupper(trim($string));
        
        $replacements = [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 
            'Ú' => 'U', 'Ü' => 'U'
        ];
        
        return strtr($string, $replacements);
    }

    private function normalizeExcelData(array $rows, array $columnMap): array
    {
        $normalizedData = [];
        
        // Normalizar headers (fila 2, asumiendo que $rows[2] son los encabezados)
        $normalizedHeaders = array_map([$this, 'normalizeString'], array_map('trim', $rows[2]));
        
        // Obtener índices de columnas necesarias
        $columnIndexes = [];
        foreach ($columnMap as $excelHeader => $dbField) {
            $normalizedHeader = $this->normalizeString(trim($excelHeader));
            $colIndex = array_search($normalizedHeader, $normalizedHeaders);
            if ($colIndex === false) {
                throw new \Exception("Columna obligatoria no encontrada: {$excelHeader}");
            }
            $columnIndexes[$dbField] = $colIndex;
        }
        
        // Normalizar filas de datos (desde fila 3 en adelante)
        for ($i = 3; $i < count($rows); $i++) {
            $row = $rows[$i];
            $normalizedRow = [];
            
            foreach ($columnIndexes as $dbField => $colIndex) {
                $value = isset($row[$colIndex]) ? trim($row[$colIndex]) : null;
                $normalizedRow[$dbField] = $value ? $this->normalizeString($value) : null;
            }
            
            $normalizedData[] = $normalizedRow;
        }
        
        return [
            'headers' => $normalizedHeaders,
            'columnIndexes' => $columnIndexes,
            'rows' => $normalizedData
        ];
    }
}