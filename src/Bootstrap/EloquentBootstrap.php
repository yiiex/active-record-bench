<?php

declare(strict_types=1);

namespace Bench\Bootstrap;

use Illuminate\Database\Capsule\Manager as Capsule;

final class EloquentBootstrap
{
    private static bool $booted = false;

    public static function boot(string $dbPath): void
    {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => $dbPath,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
