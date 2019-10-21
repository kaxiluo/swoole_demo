<?php

namespace App\Web;


use Swoole\Http\Server;

class HttpServer
{
    public function start()
    {
        $http = new Server(config('web.listen_ip'), config('web.listen_port'));

        //$http->set([]);

        $http->on('request', function ($request, $response) {
            $response->header("Content-Type", "text/html; charset=utf-8");
            $response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
        });

        $http->start();
    }
}