<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Bench\Schema\Seeder;
use Bench\Support\Env;

/**
 * The bootstrap tests need a database. Use a throwaway seeded file so the tests
 * never touch the project's bench.sqlite.
 */
$dbPath = sys_get_temp_dir() . '/bench-test-' . getmypid() . '.sqlite';
@unlink($dbPath);
@unlink($dbPath . '-wal');
@unlink($dbPath . '-shm');

putenv('BENCH_DB_PATH=' . $dbPath);

Seeder::run($dbPath);

register_shutdown_function(static function () use ($dbPath): void {
    @unlink($dbPath);
    @unlink($dbPath . '-wal');
    @unlink($dbPath . '-shm');
});
