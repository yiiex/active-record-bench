<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii2;

use Bench\Models\Yii2\BenchRow;
use Bench\Models\Yii2\Order;
use PhpBench\Attributes as Bench;

final class CrudBench extends Yii2Benchmark
{
    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchFindByPk(): void
    {
        Order::findOne(1);
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchInsert(): void
    {
        $r = new BenchRow();
        $r->name = 'Bench';
        $r->email = 'bench@example.com';
        $r->phone = '123';
        $r->country = 'US';
        $r->created_at = '2024-01-01 00:00:00';
        $r->save();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchCount(): void
    {
        Order::find()->count();
    }
}
