<?php

declare(strict_types=1);

namespace Bench\Bootstrap;

use Yiisoft\Cache\ArrayCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Connection\ConnectionProvider;
use Yiisoft\Db\Sqlite\Connection;
use Yiisoft\Db\Sqlite\Driver;

final class YiisoftBootstrap
{
    private static bool $booted = false;

    public static function boot(string $dbPath): void
    {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        // Schema cache disabled: keeps the comparison fair (the other ORMs
        // have no schema cache in these benchmarks).
        $schemaCache = new SchemaCache(new ArrayCache());
        $schemaCache->setEnabled(false);
        $connection = new Connection(new Driver('sqlite:' . $dbPath), $schemaCache);
        ConnectionProvider::set($connection);
    }
}
