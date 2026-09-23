<?php

declare(strict_types=1);

namespace Bench\Support;

final class Env
{
    public static function dbPath(): string
    {
        return getenv('BENCH_DB_PATH') ?: dirname(__DIR__, 2) . '/bench.sqlite';
    }
}
