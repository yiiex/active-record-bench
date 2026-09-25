<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii2;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yii2\Order;
use PhpBench\Attributes as Bench;

final class FindBench extends Yii2Benchmark
{
    use ProvidesParams;

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchFindAll(array $params): void
    {
        Order::find()->limit($params['limit'])->all();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchFindAllByStatus(): void
    {
        Order::find()->where(['status' => 'paid'])->all();
    }
}
