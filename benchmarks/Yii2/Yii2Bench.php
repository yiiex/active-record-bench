<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yii2;

use Bench\Bootstrap\Yii2Bootstrap;
use Bench\Models\Yii2\Customer;
use Bench\Models\Yii2\Order;
use Bench\Models\Yii2\Product;
use Bench\Support\Env;
use PhpBench\Attributes as Bench;

final class Yii2Bench
{
    public function __construct()
    {
        Yii2Bootstrap::boot(Env::dbPath());
    }

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
    public function benchFindAll(): void
    {
        Order::find()->limit(200)->all();
    }

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
    public function benchHasManyEager(): void
    {
        Customer::find()->with('orders')->where(['id' => 1])->one();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchManyManyEager(): void
    {
        Product::find()->with('categories')->limit(50)->all();
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
        Order::find()->count();
    }
}
