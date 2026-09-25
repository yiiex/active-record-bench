<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii1x;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yii1x\Order;
use PhpBench\Attributes as Bench;

final class ScopeBench extends Yii1xBenchmark
{
    use ProvidesParams;

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchScopedNestedEager(array $params): void
    {
        Order::model()->with([
            'customer' => ['scopes' => 'active'],
            'items' => ['with' => ['product' => ['scopes' => 'active']]],
        ])->findAll(['limit' => $params['limit']]);
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchNestedEagerNoScopes(array $params): void
    {
        Order::model()->with(['customer', 'items.product'])->findAll(['limit' => $params['limit']]);
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchNestedEagerRawWhere(array $params): void
    {
        Order::model()->with([
            'customer' => ['condition' => 'customer.status = :s', 'params' => [':s' => 'active']],
            'items' => ['with' => ['product' => ['condition' => 'product.is_active = :a', 'params' => [':a' => 1]]]],
        ])->findAll(['limit' => $params['limit']]);
    }
}
