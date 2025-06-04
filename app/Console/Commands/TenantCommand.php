<?php

namespace App\Console\Commands;

use App\Multitenancy\PGSchema;
use Illuminate\Console\Command;

class TenantCommand extends Command
{

    protected $signature = 'tenant:migrate {--schema= : nombre del esquema}';

    protected $description = 'migracion todos los esquemas';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        set_time_limit(0);

        $pg=new PGSchema();
        $schemas=$pg->select()->when($this->option('schema'),function($q){
            $q->where('table_schema',$this->option('schema'));
        })->get();
        foreach ($schemas as $value) {
            $this->line('<info>actualizado... ' . $value->esquema . '</info> ');
            $pg->migrate($value->esquema);
        }
        $this->line('');
        $this->line('<info>Consulta ejecutada correctamente</info>');
        $this->line('<info>total['.count($schemas).']</info>');
        return 0;
    }
}
