<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii2;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yii2\Order;
use PhpBench\Attributes as Bench;

final class QueryBuilderBench extends Yii2Benchmark
{
    use ProvidesParams;

    private function complexQuery()
    {
        return Order::find()
            ->where(['or', ['orders.status' => 'paid'], ['orders.status' => 'shipped']])
            ->andWhere(['between', 'orders.total', 100, 50000])
            ->joinWith('items.product.categories', false, 'INNER JOIN')
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
