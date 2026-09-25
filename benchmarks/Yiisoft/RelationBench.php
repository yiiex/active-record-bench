<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yiisoft;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yiisoft\Order;
use Bench\Models\Yiisoft\Product;
use PhpBench\Attributes as Bench;

final class RelationBench extends YiisoftBenchmark
{
    use ProvidesParams;

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchBelongsToEager(): void
    {
        Order::query()->with('customer')->limit(50)->all();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchManyManyEager(): void
    {
        Product::query()->with('categories')->limit(50)->all();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchFindAllWithRelations(array $params): void
    {
        Order::query()->with('customer', 'items')->limit($params['limit'])->all();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchDeepEager(array $params): void
    {
        Order::query()->with(
            'customer.addresses',
            'items.product.categories.parent',
            'payment',
            'shipment',
            'events',
        )->limit($params['limit'])->all();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchEagerItemsWhereIn(array $params): void
    {
        Order::query()->with('items')->andWhere(['<=', 'orders.id', $params['limit']])->all();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchEagerItemsJoin(array $params): void
    {
        // JOIN-only: связи НЕ заполняются (в отличие от yii1x together(), который заполняет и дедуплицирует)
        Order::query()->innerJoinWith('items', false)->andWhere(['<=', 'orders.id', $params['limit']])->all();
    }
}
