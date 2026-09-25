<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii2;

use Bench\Bootstrap\Yii2Bootstrap;
use Bench\Support\Env;
use PhpBench\Attributes\Groups;

#[Groups(['yii2'])]
abstract class Yii2Benchmark
{
    public function __construct()
    {
        Yii2Bootstrap::boot(Env::dbPath());
    }
}
