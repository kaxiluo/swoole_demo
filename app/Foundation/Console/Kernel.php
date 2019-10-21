<?php

namespace App\Foundation\Console;

use App\Contracts\Console\Kernel as KernelContract;
use App\Events\Dispatcher;
use App\Foundation\Application;
use App\Foundation\Console\Commands\WebServerStartCommand;
use Symfony\Component\Console\Application as SymfonyApplication;

class Kernel implements KernelContract
{
    protected $app;

    protected $events;

    private $artisan;

    protected $commands = [
        WebServerStartCommand::class,
    ];

    protected $bootstrappers = [
        \App\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \App\Foundation\Bootstrap\LoadConfiguration::class,
        \App\Foundation\Bootstrap\RegisterProviders::class,
    ];

    public function __construct(Application $app, Dispatcher $events)
    {
        $this->app = $app;
        $this->events = $events;
    }

    public function handle($input, $output = null)
    {
        $this->bootstrap();

        return $this->getArtisan()->run($input, $output);
    }

    public function bootstrap()
    {
        if (!$this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    protected function getArtisan()
    {
        if (is_null($this->artisan)) {
            $this->artisan = new SymfonyApplication("Tim Master", $this->app->version());
            foreach ($this->commands as $command) {
                $this->artisan->add(new $command());
            }
        }

        return $this->artisan;
    }

    /**
     * Get all of the commands registered with the console.
     *
     * @return array
     */
    public function all()
    {
        //todo
    }
}
