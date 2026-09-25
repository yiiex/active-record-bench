<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yiisoft;

use Bench\Bootstrap\YiisoftBootstrap;
use Bench\Support\Env;
use PhpBench\Attributes\Groups;

#[Groups(['yiisoft'])]
abstract class YiisoftBenchmark
{
    public function __construct()
    {
        YiisoftBootstrap::boot(Env::dbPath());
    }
}
