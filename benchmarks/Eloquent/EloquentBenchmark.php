<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Eloquent;

use Bench\Bootstrap\EloquentBootstrap;
use Bench\Support\Env;
use PhpBench\Attributes\Groups;

#[Groups(['eloquent'])]
abstract class EloquentBenchmark
{
    public function __construct()
    {
        EloquentBootstrap::boot(Env::dbPath());
    }
}
