<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii1x;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yii1x\Order;
use PhpBench\Attributes as Bench;

final class FindBench extends Yii1xBenchmark
{
    use ProvidesParams;

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchFindAll(array $params): void
    {
        Order::model()->findAll(['limit' => $params['limit']]);
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchFindAllByStatus(): void
    {
        Order::model()->findAllByAttributes(['status' => 'paid']);
    }
}
