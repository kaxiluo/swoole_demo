<?php

namespace App\Foundation\Console\Commands;

use App\Web\HttpServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WebServerStartCommand extends Command
{
    protected static $defaultName = 'web:start';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Start Web Server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Web Server');
        $io->table(
            [
                'System',
                'PHP Version',
                'Swoole Version',
                'Worker Num',
            ],
            [
                [
                    PHP_OS,
                    PHP_VERSION,
                    \swoole_version(),
                    \swoole_cpu_num(),
                ]
            ]
        );

        $server = new HttpServer();
        $server->start();
    }
}