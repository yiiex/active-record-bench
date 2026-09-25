<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii2;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yii2\Order;
use PhpBench\Attributes as Bench;

final class ScopeBench extends Yii2Benchmark
{
    use ProvidesParams;

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchScopedNestedEager(array $params): void
    {
        Order::find()->with([
            'customer' => fn($q) => $q->active(),
            'items.product' => fn($q) => $q->active(),
        ])->limit($params['limit'])->all();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchNestedEagerNoScopes(array $params): void
    {
        Order::find()->with(['customer', 'items.product'])->limit($params['limit'])->all();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchNestedEagerRawWhere(array $params): void
    {
        Order::find()->with([
            'customer' => fn ($q) => $q->andWhere(['status' => 'active']),
            'items.product' => fn ($q) => $q->andWhere(['is_active' => 1]),
        ])->limit($params['limit'])->all();
    }
}
