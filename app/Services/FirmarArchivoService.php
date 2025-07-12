<?php

namespace App\Services;

class FirmaArchivoService
{
    protected $privateKeyPath;
    protected $privateKeyPass;

    public function __construct($privateKeyPath = null, $privateKeyPass = null)
    {
        $this->privateKeyPath = $privateKeyPath ?? base_path('clave_privada.pem');
        $this->privateKeyPass = $privateKeyPass;
    }

    /**
     * Firma un archivo y retorna la firma en base64.
     */
    public function firmarArchivo($filePath)
    {
        if (!file_exists($this->privateKeyPath)) {
            throw new \Exception("No se encontró la clave privada.");
        }
        if (!file_exists($filePath)) {
            throw new \Exception("No se encontró el archivo a firmar.");
        }

        $pkey = file_get_contents($this->privateKeyPath);
        $privateKey = openssl_pkey_get_private($pkey, $this->privateKeyPass);

        if (!$privateKey) {
            throw new \Exception("No se pudo cargar la clave privada. ¿Contraseña incorrecta?");
        }

        $data = file_get_contents($filePath);

        // Firma con PSS y SHA256
        $ok = openssl_sign(
            $data,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        openssl_free_key($privateKey);

        if (!$ok) {
            throw new \Exception("No se pudo firmar el archivo.");
        }

        return base64_encode($signature);
    }

    /**
     * Guarda la firma en un archivo.
     */
    public function firmarYGuardar($filePath, $outputPath)
    {
        $firma = $this->firmarArchivo($filePath);
        file_put_contents($outputPath, $firma);
        return $outputPath;
    }
}