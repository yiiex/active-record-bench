<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Eloquent;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Eloquent\Order;
use PhpBench\Attributes as Bench;

final class QueryBuilderBench extends EloquentBenchmark
{
    use ProvidesParams;

    private function complexQuery()
    {
        return Order::query()
            ->where(function ($q) {
                $q->where('status', 'paid')->orWhere('status', 'shipped');
            })
            ->whereBetween('total', [100, 50000])
            ->whereHas('items', function ($q) {
                $q->whereHas('product', function ($q2) {
                    $q2->whereHas('categories', function ($q3) {
                        $q3->whereIn('id', [1, 2, 3]);
                    });
                });
            })
            ->orderByDesc('total')
            ->limit(50);
    }

    #[Bench\ParamProviders(['provideLoopCounts'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(1)]
    #[Bench\Iterations(3)]
    public function benchComplexQueryBuild(array $params): void
    {
        $n = $params['n'];
        for ($i = 0; $i < $n; $i++) {
            $this->complexQuery()->toSql();
        }
    }

    #[Bench\ParamProviders(['provideLoopCounts'])]
    #[Bench\Warmup(1)]
    #[Bench\Revs(1)]
    #[Bench\Iterations(3)]
    public function benchComplexQueryExecute(array $params): void
    {
        $n = $params['n'];
        for ($i = 0; $i < $n; $i++) {
            $this->complexQuery()->get();
        }
    }
}
