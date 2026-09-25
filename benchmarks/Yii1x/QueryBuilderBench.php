<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii1x;

use Bench\Benchmarks\Support\ProvidesParams;
use Bench\Models\Yii1x\Order;
use PhpBench\Attributes as Bench;

final class QueryBuilderBench extends Yii1xBenchmark
{
    use ProvidesParams;

    private function complexQuery()
    {
        return Order::queryBuilder()
            ->where(function (\Yii1x\ActiveRecord\ConditionBuilder $cb) {
                $cb->where('status', 'paid')
                   ->where('status', 'shipped', operator: 'OR');
            })
            ->whereBetween('total', 100, 50000)
            ->whereRelation('items', function (\Yii1x\ActiveRecord\ConditionBuilder $cb) {
                $cb->whereRelation('product', function (\Yii1x\ActiveRecord\ConditionBuilder $cb2) {
                    $cb2->whereRelation('categories', function (\Yii1x\ActiveRecord\ConditionBuilder $cb3) {
                        $cb3->whereIn('id', [1, 2, 3]);
                    });
                });
            })
            ->orderBy('total DESC')
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
            $qb = $this->complexQuery();
            Order::model()->getCommandBuilder()->createFindCommand(
                Order::model()->getTableSchema(),
                $qb->criteria
            )->getText();
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
            $this->complexQuery()->findAll();
        }
    }
}
