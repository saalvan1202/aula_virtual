<?php

namespace App\Console\Commands;

use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\User;
use App\Services\Variable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FakerDevCommand extends Command
{

    protected $signature = 'faker:generate {registro=docentes}
                        {--cantidad= : cantidad de registros}';

    protected $description = 'creacion de datos falsos para desarrollo';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
       set_time_limit(0);
       if(config('app.env') != 'local' || config('app.debug') ===false){
           $this->line('<line>desabilitado</line>');
           return 0;
       }
       $cantidad = (int)$this->option('cantidad')??1;
       $registro = $this->argument('registro');
       if(method_exists($this, $registro)){
           DB::transaction(function() use ($registro,$cantidad){
               $this->{$registro}($cantidad);
           });
           $this->line('<line>creacion de '.$cantidad.' datos  '.$registro.'</line>');
       }
    }
    public function docentes($cantidad)
    {
        $per = Persona::factory()->count($cantidad)
            ->has(
                User::factory()
                    ->count(1)
                    ->state(function (array $attributes, Persona $per) {
                        return [
                            'usuario' => $per->numero_documento,
                            'password'=>bcrypt($per->numero_documento),
                            'id_perfil'=>Variable::DOCENTE
                        ];
                    })
                ,'usuarios')
            ->create();
    }
    public function estudiantes($cantidad)
    {
        $per = Persona::factory()->count($cantidad)
            ->has(
                User::factory()
                    ->count(1)
                    ->state(function (array $attributes, Persona $per) {
                        return [
                            'usuario' => $per->numero_documento,
                            'password'=>bcrypt($per->numero_documento),
                            'id_perfil'=>Variable::ESTUDIANTE
                        ];
                    })->has(
                        Estudiante::factory()
                            ->count(1)
                        ,'estudiante')
                ,'usuarios')
            ->create();
    }
}
