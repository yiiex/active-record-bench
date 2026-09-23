<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Bench\Schema\Seeder;
use Bench\Support\Env;

Seeder::run(Env::dbPath());
