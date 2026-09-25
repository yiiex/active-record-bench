<?php

declare(strict_types=1);

namespace Bench\Benchmarks\Support;

/**
 * Shared @ParamProviders used across benchmark classes.
 */
trait ProvidesParams
{
    public function provideLimits(): iterable
    {
        yield '100' => ['limit' => 100];
        yield '250' => ['limit' => 250];
        yield '500' => ['limit' => 500];
        yield '1000' => ['limit' => 1000];
    }

    public function provideLoopCounts(): iterable
    {
        yield '100' => ['n' => 100];
        yield '250' => ['n' => 250];
        yield '500' => ['n' => 500];
        yield '1000' => ['n' => 1000];
        yield '2000' => ['n' => 2000];
    }
}
