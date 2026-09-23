<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Yiisoft;

use Bench\Bootstrap\YiisoftBootstrap;
use Bench\Models\Yiisoft\Customer;
use Bench\Models\Yiisoft\Order;
use Bench\Models\Yiisoft\Product;
use Bench\Support\Env;
use PhpBench\Attributes as Bench;

final class YiisoftBench
{
    public function __construct()
    {
        YiisoftBootstrap::boot(Env::dbPath());
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchFindByPk(): void
    {
        Order::query()->findByPk(1);
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchFindAll(): void
    {
        Order::query()->limit(200)->all();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchBelongsToEager(): void
    {
        Order::query()->with('customer')->limit(50)->all();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchHasManyEager(): void
    {
        Customer::query()->with('orders')->where(['id' => 1])->one();
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchManyManyEager(): void
    {
        Product::query()->with('categories')->limit(50)->all();
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
        Order::query()->count();
    }
}
