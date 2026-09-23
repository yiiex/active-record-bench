<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii1x;

use Bench\Bootstrap\Yii1xBootstrap;
use Bench\Models\Yii1x\Customer;
use Bench\Models\Yii1x\Order;
use Bench\Models\Yii1x\Product;
use Bench\Support\Env;
use PhpBench\Attributes as Bench;

final class Yii1xBench
{
    public function __construct()
    {
        Yii1xBootstrap::boot(Env::dbPath());
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchFindByPk(): void
    {
        Order::model()->findByPk(1);
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchFindAll(): void
    {
        Order::model()->findAll(['limit' => 200]);
    }

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
    public function benchHasManyEager(): void
    {
        Customer::model()->with('orders')->findByPk(1);
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchManyManyEager(): void
    {
        Product::model()->with('categories')->findAll(['limit' => 50]);
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchInsert(): void
    {
        $c = new Customer();
        $c->name = 'Bench';
        $c->email = 'bench@example.com';
        $c->phone = '123';
        $c->country = 'US';
        $c->created_at = '2024-01-01 00:00:00';
        $c->save();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchCount(): void
    {
        Order::model()->count();
    }
}
