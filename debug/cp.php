<?php

use Swoole\Coroutine\Channel;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Swoole\Process;

$process = new Process(function (Process $process) {
    $server = new Server('0.0.0.0', 9501, SWOOLE_BASE);
    $server->set([
        'log_file' => '/dev/null',
        'log_level' => SWOOLE_LOG_INFO,
        'worker_num' => swoole_cpu_num() * 2,
    ]);
    $server->on('workerStart', function () use ($process, $server) {
        //$server->pool = new RedisQueue();
        $server->pool = new RedisPool(64);
        $process->write('1');
    });
    $server->on('request', function (Request $request, Response $response) use ($server) {
        try {
            //$redis = new Redis();
            //$redis->connect('127.0.0.1', 6379);
            /* @var $redis Redis */
            $redis = $server->pool->get();
            $test = $redis->get('test');
            if (!$test) {
                throw new RedisException('get fail');
            }
            $server->pool->put($redis);
            $response->end($test);
        } catch (Throwable $throwable) {
            $response->status(500);
            $response->end();
        }
    });
    $server->start();
});
if ($process->start()) {
    register_shutdown_function(function () use ($process) {
        $process::kill($process->pid);
        $process::wait();
    });
    $process->read(1);
    System('ab -c 5000 -n 100000 -k http://127.0.0.1:9501/ 2>&1');
}

class RedisQueue
{
    protected $pool;

    public function __construct()
    {
        $this->pool = new SplQueue();
    }

    public function get(): Redis
    {
        if ($this->pool->isEmpty()) {
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
            return $redis;
        }
        return $this->pool->dequeue();
    }

    public function put(Redis $redis)
    {
        $this->pool->enqueue($redis);
    }

    public function close(): void
    {
        $this->pool = null;
    }
}

class RedisPool
{
    /** @var \Swoole\Coroutine\Channel */
    protected $pool;

    /**
     * RedisPool constructor.
     * @param int $size
     */
    public function __construct(int $size = 100)
    {

        $this->pool = new Channel($size);
        for ($i = 0; $i < $size; $i++) {
            try {
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379);
                $this->put($redis);
                echo 'redis ok';
            } catch (Throwable $throwable) {
                usleep(1000);
                continue;
            }
        }
    }

    public function get(): Redis
    {
        return $this->pool->pop();
    }

    public function put(Redis $redis)
    {
        $this->pool->push($redis);
    }

    public function close(): void
    {
        $this->pool->close();
        $this->pool = null;
    }
}
