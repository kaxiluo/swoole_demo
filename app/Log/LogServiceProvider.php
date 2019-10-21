<?php

namespace App\Log;

use App\Foundation\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('log', function ($app) {
            return new LogManager($app);
        });
    }
}
