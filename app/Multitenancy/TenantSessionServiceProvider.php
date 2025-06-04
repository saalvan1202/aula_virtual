<?php

namespace App\Multitenancy;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\SessionManager;
use \Illuminate\Session\SessionServiceProvider as BaseSessionServiceProvider;

class TenantSessionServiceProvider extends BaseSessionServiceProvider
{

    public function register()
    {

        $this->registerSessionManager();

        $this->registerSessionDriver();

        $this->app->singleton(StartSession::class, function () {
            return new TenantSessionMiddleware($this->app->make(SessionManager::class), function () {
                return $this->app->make(CacheFactory::class);
            });
        });
    }
}
