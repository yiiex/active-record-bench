<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Eloquent;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Eloquent\Order;
use PhpBench\Attributes as Bench;

final class ScopeBench extends EloquentBenchmark
{
    use ProvidesParams;

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchScopedNestedEager(array $params): void
    {
        Order::with([
            'customer' => fn($q) => $q->active(),
            'items.product' => fn($q) => $q->active(),
        ])->limit($params['limit'])->get();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchNestedEagerNoScopes(array $params): void
    {
        Order::with(['customer', 'items.product'])->limit($params['limit'])->get();
    }

    #[Bench\ParamProviders(['provideLimits'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(20)]
    #[Bench\Iterations(3)]
    public function benchNestedEagerRawWhere(array $params): void
    {
        Order::with([
            'customer' => fn ($q) => $q->where('status', 'active'),
            'items.product' => fn ($q) => $q->where('is_active', 1),
        ])->limit($params['limit'])->get();
    }
}
