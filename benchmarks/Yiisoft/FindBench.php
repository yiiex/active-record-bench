<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yiisoft;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yiisoft\Order;
use PhpBench\Attributes as Bench;

final class FindBench extends YiisoftBenchmark
{
    use ProvidesParams;

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchFindAll(array $params): void
    {
        Order::query()->limit($params['limit'])->all();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchFindAllByStatus(): void
    {
        Order::query()->where(['status' => 'paid'])->all();
    }
}
