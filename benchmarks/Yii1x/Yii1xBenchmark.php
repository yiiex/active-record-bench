<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii1x;

use Bench\Bootstrap\Yii1xBootstrap;
use Bench\Support\Env;
use PhpBench\Attributes\Groups;

#[Groups(['yii1x'])]
abstract class Yii1xBenchmark
{
    public function __construct()
    {
        Yii1xBootstrap::boot(Env::dbPath());
    }
}
