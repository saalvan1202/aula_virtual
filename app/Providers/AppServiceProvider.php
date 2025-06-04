<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
            if (env('APP_ENV') === 'production') {
        URL::forceScheme('https');
    }

        Validator::extendImplicit('required_zero', function ($attribute, $value, $parameters, $validator) {
            return $value !=0;
        },'el :attribute es obligatorio');
    }
}
