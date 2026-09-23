<?php

declare(strict_types=1);

namespace Bench\Bootstrap;

use Bench\Support\SimpleContainer;
use Yii1x\ActiveRecord\Db\DbConnection;
use Yii1x\ActiveRecord\ORMContext;

final class Yii1xBootstrap
{
    private static bool $booted = false;

    public static function boot(string $dbPath): void
    {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        $connection = new DbConnection('sqlite:' . $dbPath, '', '', 'default');

        $container = new SimpleContainer(['default' => $connection]);

        ORMContext::bootstrap($container);
    }
}
