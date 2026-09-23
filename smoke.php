<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Bench\Support\Env;

$dbPath = Env::dbPath();

function section(string $name): void
{
    echo "\n== $name ==\n";
}

function check(string $label, callable $fn): void
{
    try {
        $result = $fn();
        echo '  OK   ' . $label;
        if ($result !== null) {
            echo ' -> ' . $result;
        }
        echo "\n";
    } catch (\Throwable $e) {
        echo '  FAIL ' . $label . ': ' . get_class($e) . ': ' . $e->getMessage() . "\n";
    }
}

// ---------------------------------------------------------------- Eloquent
section('Eloquent');
Bench\Bootstrap\EloquentBootstrap::boot($dbPath);
check('findByPk', fn () => Bench\Models\Eloquent\Order::find(1)?->number);
check('belongsTo eager', function () {
    $orders = Bench\Models\Eloquent\Order::with('customer')->limit(50)->get();

    return count($orders) . ' orders, first customer=' . $orders[0]->customer->name;
});
check('hasMany eager', function () {
    $c = Bench\Models\Eloquent\Customer::with('orders')->find(1);

    return 'customer #1 orders=' . count($c->orders);
});
check('manyMany eager', function () {
    $p = Bench\Models\Eloquent\Product::with('categories')->find(1);

    return 'product #1 categories=' . count($p->categories);
});
check('count', fn () => 'orders=' . Bench\Models\Eloquent\Order::count());
check('insert', function () {
    $c = new Bench\Models\Eloquent\Customer();
    $c->name = 'Smoke';
    $c->email = 'smoke@example.com';
    $c->phone = '123';
    $c->country = 'US';
    $c->created_at = '2024-01-01 00:00:00';
    $c->save();

    return 'new id=' . $c->id;
});

// ------------------------------------------------------------------- Yii2
section('Yii2');
Bench\Bootstrap\Yii2Bootstrap::boot($dbPath);
check('findByPk', fn () => Bench\Models\Yii2\Order::findOne(1)?->number);
check('belongsTo eager', function () {
    $orders = Bench\Models\Yii2\Order::find()->with('customer')->limit(50)->all();

    return count($orders) . ' orders, first customer=' . $orders[0]->customer->name;
});
check('hasMany eager', function () {
    $c = Bench\Models\Yii2\Customer::find()->with('orders')->where(['id' => 1])->one();

    return 'customer #1 orders=' . count($c->orders);
});
check('manyMany eager', function () {
    $p = Bench\Models\Yii2\Product::find()->with('categories')->where(['id' => 1])->one();

    return 'product #1 categories=' . count($p->categories);
});
check('count', fn () => 'orders=' . Bench\Models\Yii2\Order::find()->count());
check('insert', function () {
    $c = new Bench\Models\Yii2\Customer();
    $c->name = 'Smoke';
    $c->email = 'smoke2@example.com';
    $c->phone = '123';
    $c->country = 'US';
    $c->created_at = '2024-01-01 00:00:00';
    $c->save();

    return 'new id=' . $c->id;
});

// --------------------------------------------------------------- Yiisoft
section('Yiisoft');
Bench\Bootstrap\YiisoftBootstrap::boot($dbPath);
check('findByPk', fn () => Bench\Models\Yiisoft\Order::query()->findByPk(1)?->number);
check('belongsTo eager', function () {
    $orders = Bench\Models\Yiisoft\Order::query()->with('customer')->limit(50)->all();

    return count($orders) . ' orders, first customer=' . $orders[0]->relation('customer')->name;
});
check('hasMany eager', function () {
    $c = Bench\Models\Yiisoft\Customer::query()->with('orders')->where(['id' => 1])->one();

    return 'customer #1 orders=' . count($c->relation('orders'));
});
check('manyMany eager', function () {
    $p = Bench\Models\Yiisoft\Product::query()->with('categories')->where(['id' => 1])->one();

    return 'product #1 categories=' . count($p->relation('categories'));
});
check('count', fn () => 'orders=' . Bench\Models\Yiisoft\Order::query()->count());
check('insert', function () {
    $c = new Bench\Models\Yiisoft\Customer();
    $c->name = 'Smoke';
    $c->email = 'smoke3@example.com';
    $c->phone = '123';
    $c->country = 'US';
    $c->created_at = '2024-01-01 00:00:00';
    $c->save();

    return 'new id=' . $c->id;
});

// ----------------------------------------------------------------- Yii1x
section('Yii1x');
Bench\Bootstrap\Yii1xBootstrap::boot($dbPath);
check('findByPk', fn () => Bench\Models\Yii1x\Order::model()->findByPk(1)?->number);
check('belongsTo eager', function () {
    $orders = Bench\Models\Yii1x\Order::model()->with('customer')->findAll(['limit' => 50]);

    return count($orders) . ' orders, first customer=' . $orders[0]->customer->name;
});
check('hasMany eager', function () {
    $c = Bench\Models\Yii1x\Customer::model()->with('orders')->findByPk(1);

    return 'customer #1 orders=' . count($c->orders);
});
check('manyMany eager', function () {
    $p = Bench\Models\Yii1x\Product::model()->with('categories')->findByPk(1);

    return 'product #1 categories=' . count($p->categories);
});
check('count', fn () => 'orders=' . Bench\Models\Yii1x\Order::model()->count());
check('insert', function () {
    $c = new Bench\Models\Yii1x\Customer();
    $c->name = 'Smoke';
    $c->email = 'smoke4@example.com';
    $c->phone = '123';
    $c->country = 'US';
    $c->created_at = '2024-01-01 00:00:00';
    $c->save();

    return 'new id=' . $c->id;
});

echo "\nDone.\n";
