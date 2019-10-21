<?php

namespace App\Foundation\Bootstrap;

use App\Foundation\Application;
use Dotenv\Dotenv;

class LoadEnvironmentVariables
{
    public function bootstrap(Application $app)
    {
        $this->createDotenv($app)->overload();
    }

    /**
     * @param Application $app
     * @return Dotenv
     */
    protected function createDotenv($app)
    {
        return Dotenv::create(
            $app->environmentPath(),
            $app->environmentFile()
        );
    }
}
