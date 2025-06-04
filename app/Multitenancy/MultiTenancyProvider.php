<?php

namespace App\Multitenancy;

use Illuminate\Support\ServiceProvider;

class MultiTenancyProvider extends ServiceProvider
{
    public function boot()
    {
        set_time_limit(0);
        if(is_multitenancy()){
            $host=new HostFinder();
            $hostName=$host->findForRequest($this->app['request']);
            if($this->app->runningInConsole()){
                $hostName=$host->findForConsole();
            }
            $this->configureRequests($hostName);
        }

    }
    public function configureRequests($schema)
    {
        (new Tenant())->create($schema)->configure();
    }
}
