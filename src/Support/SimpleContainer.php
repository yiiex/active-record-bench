<?php

declare(strict_types=1);

namespace Bench\Support;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Minimal PSR-11 container used to bootstrap the yii1x ActiveRecord ORMContext.
 */
final class SimpleContainer implements ContainerInterface
{
    /** @var array<string, mixed> */
    private array $services = [];

    public function __construct(array $services = [])
    {
        $this->services = $services;
    }

    public function set(string $id, mixed $value): static
    {
        $this->services[$id] = $value;

        return $this;
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->services)) {
            return $this->services[$id];
        }

        throw new class($id) extends \Exception implements NotFoundExceptionInterface {
            public function __construct(string $id)
            {
                parent::__construct("Service '$id' not found");
            }
        };
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }
}
