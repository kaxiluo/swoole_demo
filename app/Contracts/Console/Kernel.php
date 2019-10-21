<?php

namespace App\Contracts\Console;

interface Kernel
{
    public function handle($input, $output = null);

    public function all();
}
