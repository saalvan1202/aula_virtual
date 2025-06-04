<?php
namespace App\Multitenancy;
use Illuminate\Support\Facades\DB;

class Tenant
{
    protected $schema;
    public function create($schema): Tenant
    {
        $this->schema=$schema;
        return $this;
    }
    public function getSchema()
    {
        return $this->schema;
    }
    public function configure()
    {
        set_time_limit(0);
        if ($this->isCurrent()) {
            return $this;
        }
        $schema=$this->getSchema();
        config(["database.connections.pgsql.schema"=>$schema]);
        DB::purge('pgsql');
        DB::setDefaultConnection('pgsql');

        app()->forgetInstance('currentTenant');
        app()->instance('currentTenant', $this);
        $this->createFile( $schema);
    }
    public function createFile($schema)
    {
        //Storage::makeDirectory('archivos/'.$schema);
    }
    public function isCurrent(): bool
    {
        $current=static::current();
        if($current==null){
            return false;
        }
        return $current->getSchema() === $this->getSchema();
    }
    public static function current()
    {
        $containerKey = 'currentTenant';

        if (! app()->has($containerKey)) {
            return null;
        }
        return app($containerKey);
    }
}
