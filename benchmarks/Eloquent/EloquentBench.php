<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Eloquent;

use Bench\Bootstrap\EloquentBootstrap;
use Bench\Models\Eloquent\Customer;
use Bench\Models\Eloquent\Order;
use Bench\Models\Eloquent\Product;
use Bench\Support\Env;
use PhpBench\Attributes as Bench;

final class EloquentBench
{
    public function __construct()
    {
        EloquentBootstrap::boot(Env::dbPath());
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchFindByPk(): void
    {
        Order::find(1);
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(100)]
    #[Bench\Iterations(3)]
    public function benchFindAll(): void
    {
        Order::query()->limit(200)->get();
    }

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
    public function benchHasManyEager(): void
    {
        Customer::with('orders')->find(1);
    }

    #[Bench\Warmup(1)]
    #[Bench\Revs(50)]
    #[Bench\Iterations(3)]
    public function benchManyManyEager(): void
    {
        Product::with('categories')->limit(50)->get();
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
