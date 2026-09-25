<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yiisoft;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yiisoft\Order;
use PhpBench\Attributes as Bench;

final class QueryBuilderBench extends YiisoftBenchmark
{
    use ProvidesParams;

    private function complexQuery()
    {
        return Order::query()
            ->where(['or', ['orders.status' => 'paid'], ['orders.status' => 'shipped']])
            ->andWhere(['between', 'orders.total', 100, 50000])
            ->innerJoinWith('items.product.categories', false)
            ->andWhere(['in', 'categories.id', [1, 2, 3]])
            ->distinct()
            ->orderBy('orders.total DESC')
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
            $this->complexQuery()->createCommand()->getSql();
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
            $this->complexQuery()->all();
        }
    }
}
