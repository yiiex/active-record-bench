<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Eloquent;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Eloquent\Order;
use Bench\Models\Eloquent\Product;
use PhpBench\Attributes as Bench;

final class RelationBench extends EloquentBenchmark
{
    use ProvidesParams;

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchBelongsToEager(): void
    {
        Order::with('customer')->limit(50)->get();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchManyManyEager(): void
    {
        Product::with('categories')->limit(50)->get();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchFindAllWithRelations(array $params): void
    {
        Order::with('customer', 'items')->limit($params['limit'])->get();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchDeepEager(array $params): void
    {
        Order::with([
            'customer.addresses',
            'items.product.categories.parent',
            'payment',
            'shipment',
            'events',
        ])->limit($params['limit'])->get();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(10)]
    #[Bench\Iterations(3)]
    public function benchEagerItemsWhereIn(array $params): void
    {
        Order::with('items')->where('id', '<=', $params['limit'])->get();
    }
}
