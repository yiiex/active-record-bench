<?php

declare(strict_types=1);

namespace Bench\Tests;

use Bench\Bootstrap\EloquentBootstrap;
use Bench\Bootstrap\Yii1xBootstrap;
use Bench\Bootstrap\Yii2Bootstrap;
use Bench\Bootstrap\YiisoftBootstrap;
use Bench\Models\Eloquent\Order as EloquentOrder;
use Bench\Models\Yii1x\Order as Yii1xOrder;
use Bench\Models\Yii2\Order as Yii2Order;
use Bench\Models\Yiisoft\Order as YiisoftOrder;
use Bench\Support\Env;
use PHPUnit\Framework\TestCase;

/**
 * Each bootstrap must bring its ORM up and give the models a working connection.
 * The database itself is seeded in tests/bootstrap.php.
 */
final class BootstrapTest extends TestCase
{
    public function testEloquentConnects(): void
    {
        EloquentBootstrap::boot(Env::dbPath());

        self::assertNotNull(EloquentOrder::find(1));
        self::assertSame(25000, EloquentOrder::query()->count());
    }

    public function testYii2Connects(): void
    {
        Yii2Bootstrap::boot(Env::dbPath());

        self::assertNotNull(Yii2Order::findOne(1));
        self::assertSame(25000, (int) Yii2Order::find()->count());
    }

    public function testYiisoftConnects(): void
    {
        YiisoftBootstrap::boot(Env::dbPath());

        self::assertNotNull(YiisoftOrder::query()->findByPk(1));
        self::assertSame(25000, (int) YiisoftOrder::query()->count());
    }

    public function testYii1xConnects(): void
    {
        Yii1xBootstrap::boot(Env::dbPath());

        self::assertNotNull(Yii1xOrder::model()->findByPk(1));
        self::assertSame(25000, (int) Yii1xOrder::model()->count());
    }
}
