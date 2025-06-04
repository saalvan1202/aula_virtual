<?php

namespace App\Multitenancy;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PGSchema
{
    public function select()
    {
        return DB::table('information_schema.tables')
            ->selectRaw('distinct table_schema as esquema')
            ->whereNotIn('table_schema',['public','information_schema'])
            ->whereRaw(' right(table_schema, 1)<>?',['_'])
            ->whereRaw(' left(table_schema, 3)<>?',['pg_'])
            ->orderBy('table_schema');
    }
    protected function tableExists($schemaName, $tableName)
    {
        $found = $this->select()->where('table_schema', $schemaName)
            ->where('table_name', $tableName)->first();
        if($found){
            return true;
        }

        return false;
    }
    public function schemaExists($schemaName)
    {
        $schema = DB::table('information_schema.schemata')
            ->select('schema_name')
            ->where('schema_name', '=', $schemaName)
            ->count();

        return ($schema > 0);
    }
    public function create($schemaName)
    {
        $query = DB::statement('CREATE SCHEMA "' . $schemaName . '"');
    }
    public function switchTo($schemaName = 'public')
    {
        $this->setSchemaInConnection($schemaName);

        if (!is_array($schemaName)) {
            $schemaName = [$schemaName];
        }

        $query = 'SET search_path TO ' . implode(',', $schemaName);


        $result = DB::statement($query);
    }
    private function setSchemaInConnection($schemaName)
    {
        $driver = DB::connection()->getConfig('driver');
        $config = Config::get("database.connections.$driver");
        $config['schema'] = $schemaName;

        DB::purge($driver);
        Config::set("database.connections.$driver", $config);
    }
    public function drop($schemaName)
    {
        $query = DB::statement('DROP SCHEMA "' . $schemaName . '" CASCADE');
    }
    public function migrate($schemaName, $args = [])
    {
        $this->switchTo($schemaName);
        if (!$this->tableExists($schemaName, 'migrations')) {
            Artisan::call('migrate:install');
        }

        Artisan::call('migrate', $args);
    }
    public function migrateRefresh($schemaName, $args = [])
    {
        $this->switchTo($schemaName);

        Artisan::call('migrate:refresh', $args);
    }
    public function migrateReset($schemaName, $args = [])
    {
        $this->switchTo($schemaName);

        Artisan::call('migrate:reset', $args);
    }
    public function migrateRollback($schemaName, $args = [])
    {
        $this->switchTo($schemaName);

        Artisan::call('migrate:rollback', $args);
    }

    /**
     * Return the current search path
     *
     * @return string
     */
    public function getSearchPath()
    {
        $query = DB::select('show search_path');
        $searchPath = array_pop($query)->search_path;

        return $searchPath;
    }
}
