<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Eloquent;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Eloquent\Order;
use PhpBench\Attributes as Bench;

final class FindBench extends EloquentBenchmark
{
    use ProvidesParams;

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchFindAll(array $params): void
    {
        Order::query()->limit($params['limit'])->get();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchFindAllByStatus(): void
    {
        Order::where('status', 'paid')->get();
    }
}
