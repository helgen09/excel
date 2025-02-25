<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class RedisProgressService
{
    public function getProgress($key)
    {
        return Redis::get($key);
    }
}