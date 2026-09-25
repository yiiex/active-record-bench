<?php

declare(strict_types=1);

namespace Bench\Tests;

use Bench\Schema\Seeder;
use PDO;
use PHPUnit\Framework\TestCase;

final class SeederTest extends TestCase
{
    private const TABLES = [
        'customers', 'addresses', 'products', 'categories', 'orders', 'order_items',
        'payments', 'shipments', 'order_events', 'comments', 'product_category', 'bench_rows',
    ];

    public function testExpectedVolumes(): void
    {
        $counts = $this->counts($this->seed());

        self::assertSame(2500, $counts['customers']);
        self::assertSame(2500, $counts['addresses']);
        self::assertSame(1000, $counts['products']);
        self::assertSame(50, $counts['categories']);
        self::assertSame(25000, $counts['orders']);
        self::assertSame(25000, $counts['payments']);
        self::assertSame(25000, $counts['shipments']);

        // Derived volumes: 5-15 items per order, 3-8 events per order,
        // 5-15 comments per product, 1-3 categories per product.
        self::assertBetween(25000 * 5, 25000 * 15, $counts['order_items'], 'order_items');
        self::assertBetween(25000 * 3, 25000 * 8, $counts['order_events'], 'order_events');
        self::assertBetween(1000 * 5, 1000 * 15, $counts['comments'], 'comments');
        self::assertBetween(1000, 1000 * 3, $counts['product_category'], 'product_category');

        // The write-only table for the insert benchmark starts empty.
        self::assertSame(0, $counts['bench_rows']);
    }

    public function testDeterministic(): void
    {
        self::assertSame($this->counts($this->seed()), $this->counts($this->seed()));
    }

    public function testForeignKeysAreIntact(): void
    {
        $pdo = new PDO('sqlite:' . $this->seed());

        self::assertSame(0, $this->scalar($pdo, 'SELECT COUNT(*) FROM orders WHERE customer_id NOT IN (SELECT id FROM customers)'));
        self::assertSame(0, $this->scalar($pdo, 'SELECT COUNT(*) FROM order_items WHERE order_id NOT IN (SELECT id FROM orders)'));
        self::assertSame(0, $this->scalar($pdo, 'SELECT COUNT(*) FROM comments WHERE product_id NOT IN (SELECT id FROM products)'));
    }

    private function seed(): string
    {
        $path = sys_get_temp_dir() . '/bench-seed-test-' . bin2hex(random_bytes(6)) . '.sqlite';
        Seeder::run($path);

        return $path;
    }

    private function counts(string $path): array
    {
        $pdo = new PDO('sqlite:' . $path);

        return array_map(
            static fn (string $table): int => (int) $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn(),
            array_combine(self::TABLES, self::TABLES),
        );
    }

    private function scalar(PDO $pdo, string $sql): int
    {
        return (int) $pdo->query($sql)->fetchColumn();
    }

    private static function assertBetween(int $min, int $max, int $actual, string $label): void
    {
        self::assertGreaterThanOrEqual($min, $actual, "$label below expected range");
        self::assertLessThanOrEqual($max, $actual, "$label above expected range");
    }
}
