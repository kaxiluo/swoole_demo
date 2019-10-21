<?php

use Swoole\Http\Server;
use Swoole\Process;

$process = new Process(function (Process $process) {
    $i = 0;
    while (true) {
        $i++;
        file_put_contents('/home/tim/debug_log/1.log', '1', FILE_APPEND);
        if ($i >= 100000) {
            break;
        }
    }
});

$process->start();

