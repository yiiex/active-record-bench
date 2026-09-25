<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii2;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yii2\Order;
use Bench\Models\Yii2\Product;
use PhpBench\Attributes as Bench;

final class RelationBench extends Yii2Benchmark
{
    use ProvidesParams;

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchBelongsToEager(): void
    {
        Order::find()->with('customer')->limit(50)->all();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchManyManyEager(): void
    {
        Product::find()->with('categories')->limit(50)->all();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchFindAllWithRelations(array $params): void
    {
        Order::find()->with('customer', 'items')->limit($params['limit'])->all();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchDeepEager(array $params): void
    {
        Order::find()->with([
            'customer.addresses',
            'items.product.categories.parent',
            'payment',
            'shipment',
            'events',
        ])->limit($params['limit'])->all();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchEagerItemsWhereIn(array $params): void
    {
        Order::find()->with('items')->andWhere(['<=', 'orders.id', $params['limit']])->all();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchEagerItemsJoin(array $params): void
    {
        // JOIN-only: связи НЕ заполняются (в отличие от yii1x together(), который заполняет и дедуплицирует)
        Order::find()->joinWith('items', false)->andWhere(['<=', 'orders.id', $params['limit']])->all();
    }
}
