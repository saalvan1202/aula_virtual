<?php

namespace App\Services;

class Variable
{
    public const SUPERADMIN = 1;//id perfil
    public const DOCENTE = 4;// id perfil
    public const ESTUDIANTE = 5;//id perfil
    public const IDMASTER=1;//id usuario
    public const PASSWORD='********';
    public const IDPERU=174;
    public const INGRESO_DIRECTO=3;

    public static function isMaster($idPerfil)
    {
        return $idPerfil==static::IDMASTER;
    }
    public static function isNotModuleUser(): array
    {
        return [static::DOCENTE,static::ESTUDIANTE];
    }
    public static function isTeacher($idPerfil)
    {
        return $idPerfil==static::DOCENTE;
    }
    public static function isStudent($idPerfil)
    {
        return $idPerfil==static::ESTUDIANTE;
    }
    public static function appName(): string
    {
        return 'PVIRTUAL';
    }
}
