<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii1x;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yii1x\Order;
use Bench\Models\Yii1x\Product;
use PhpBench\Attributes as Bench;
use Yii1x\ActiveRecord\Db\Schema\DbCriteria;

final class RelationBench extends Yii1xBenchmark
{
    use ProvidesParams;

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchBelongsToEager(): void
    {
        Order::model()->with('customer')->findAll(['limit' => 50]);
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchManyManyEager(): void
    {
        Product::model()->with('categories')->findAll(['limit' => 50]);
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchFindAllWithRelations(array $params): void
    {
        Order::model()->with('customer', 'items')->findAll(['limit' => $params['limit']]);
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchDeepEager(array $params): void
    {
        // Default yii1x strategy: nested hasMany/manyMany are merged into one
        // wide JOIN, so the whole cross-product is materialised in memory.
        Order::model()->with([
            'customer.addresses',
            'items.product.categories.parent',
            'payment',
            'shipment',
            'events',
        ])->findAll(['limit' => $params['limit']]);
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchDeepEagerNoJoin(array $params): void
    {
        // Same relations, but every nested level is loaded by its own query
        // (together => false) instead of a single wide JOIN.
        Order::model()->findAll(new DbCriteria([
            'with' => [
                'customer.addresses',
                'items' => [
                    'together' => false,
                    'with' => [
                        'product' => [
                            'together' => false,
                            'with' => [
                                'categories' => [
                                    'together' => false,
                                    'with' => ['parent' => ['together' => false]],
                                ],
                            ],
                        ],
                    ]],
                'payment',
                'shipment',
                'events',
            ],
            'limit' => $params['limit'],
        ]));
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchEagerItemsWhereIn(array $params): void
    {
        // Force the separate-query strategy: without an explicit together => false
        // the default (no LIMIT) is a JOIN, not WHERE IN.
        Order::model()->with(['items' => ['together' => false]])->findAll([
            'condition' => 't.id <= :n',
            'params' => [':n' => $params['limit']],
        ]);
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchEagerItemsJoin(array $params): void
    {
        // настоящий JOIN-eager: связи заполняются из JOIN-результата (с дедупликацией)
        Order::model()->with('items')->together()->findAll([
            'condition' => 't.id <= :n',
            'params' => [':n' => $params['limit']],
        ]);
    }
}
