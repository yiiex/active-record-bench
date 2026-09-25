<?php

declare(strict_types=1);

namespace Bench\Schema;

use PDO;

/**
 * Deterministic database seeder.
 *
 * Uses raw PDO (not any ORM) so that seeding is orthogonal to what is being
 * benchmarked. Batched multi-row inserts wrapped in a transaction keep SQLite
 * seeding fast even at hundreds of thousands of rows.
 */
final class Seeder
{
    private PDO $pdo;

    private int $customers;
    private int $products;
    private int $categories;

    public static function run(string $dbPath): void
    {
        (new self($dbPath))->seed();
    }

    private function __construct(string $dbPath)
    {
        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('PRAGMA foreign_keys = ON');
    }

    private function seed(): void
    {
        $this->customers = (int)(getenv('SEED_CUSTOMERS') ?: 2500);
        $this->products = (int)(getenv('SEED_PRODUCTS') ?: 1000);
        $this->categories = (int)(getenv('SEED_CATEGORIES') ?: 50);
        $orders = (int)(getenv('SEED_ORDERS') ?: 25000);

        mt_srand(20240901);

        $this->pdo->exec(file_get_contents(__DIR__ . '/schema.sql'));

        // The insert benchmark is write-only; always start it from an empty table.
        $this->pdo->exec('DELETE FROM bench_rows');

        $this->pdo->beginTransaction();

        try {
            $this->seedCategories();
            $this->seedCustomers();
            $this->seedProducts();
            $this->seedProductCategory();
            $this->seedOrders($orders);
            $this->seedComments();
            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }

        // Populate optimizer statistics (sqlite_stat1) so the query planner
        // picks the right indexes for the benchmark queries.
        $this->pdo->exec('ANALYZE');

        $this->report();
    }

    private function seedCategories(): void
    {
        $rows = [];
        for ($i = 1; $i <= $this->categories; $i++) {
            $rows[] = [
                $i,
                "Category $i",
                "category-$i",
                "Description for category $i",
                $i > 3 ? (int)floor($i / 3) : null,
                $i,
                1,
            ];
        }
        $this->insertBatch('categories', ['id', 'name', 'slug', 'description', 'parent_id', 'position', 'is_active'], $rows);
    }

    private function seedCustomers(): void
    {
        $this->insertRows($this->customers, function (int $i) {
            return [
                $i,
                "Customer $i",
                "customer$i@example.com",
                sprintf('+1-%03d-%03d-%04d', $i % 1000, $i % 1000, $i % 10000),
                ['US', 'DE', 'FR', 'GB', 'RU'][$i % 5],
                ['active', 'inactive', 'blocked'][$i % 3],
                $i % 2,
                $i * 10,
                $i % 4 === 0 ? "Note $i" : null,
                $this->date($i),
                $i % 3 === 0 ? $this->date($i + 5) : null,
            ];
        }, 'customers', ['id', 'name', 'email', 'phone', 'country', 'status', 'is_verified', 'points', 'notes', 'created_at', 'last_login_at']);

        $rows = [];
        for ($i = 1; $i <= $this->customers; $i++) {
            $rows[] = [
                $i,
                $i,
                ['US', 'DE', 'FR', 'GB', 'RU'][$i % 5],
                "City $i",
                "Street $i",
                sprintf('%05d', $i),
                sprintf('+1-%03d-%03d-%04d', $i % 1000, $i % 1000, $i % 10000),
                1,
            ];
        }
        $this->insertBatch('addresses', ['id', 'customer_id', 'country', 'city', 'street', 'zip', 'phone', 'is_default'], $rows);
    }

    private function seedProducts(): void
    {
        $this->insertRows($this->products, function (int $i) {
            return [
                $i,
                "Product $i",
                "product-$i",
                sprintf('SKU-%06d', $i),
                ['Acme', 'Globex', 'Initech', 'Umbrella'][$i % 4],
                "Full description of product $i.",
                $this->money(100, 100000),
                $this->money(50, 50000),
                mt_rand(0, 1000),
                $this->money(10, 5000),
                1,
                $this->money(0, 2500),
                $this->money(0, 3000),
                mt_rand(0, 500) / 10,
                mt_rand(0, 100000),
                mt_rand(0, 5000),
                $this->date($i),
                $this->date($i + 3),
            ];
        }, 'products', ['id', 'name', 'slug', 'sku', 'brand', 'description', 'price', 'cost_price', 'stock', 'weight', 'is_active', 'tax_rate', 'discount', 'rating', 'views', 'sold_count', 'created_at', 'updated_at']);
    }

    private function seedProductCategory(): void
    {
        $rows = [];
        for ($p = 1; $p <= $this->products; $p++) {
            $n = mt_rand(1, 3);
            $chosen = [];
            while (count($chosen) < $n) {
                $chosen[mt_rand(1, $this->categories)] = true;
            }
            foreach (array_keys($chosen) as $categoryId) {
                $rows[] = [$p, $categoryId];
            }
        }
        $this->insertBatch('product_category', ['product_id', 'category_id'], $rows);
    }

    private function seedOrders(int $count): void
    {
        $this->insertRows($count, function (int $i) {
            $subtotal = $this->money(1000, 500000);
            $discount = $this->money(0, 10000);
            $tax = $this->money(0, 30000);
            $shipping = $this->money(0, 5000);

            return [
                $i,
                mt_rand(1, $this->customers),
                sprintf('ORD-%010d', $i),
                ['new', 'paid', 'shipped', 'done'][$i % 4],
                ['USD', 'EUR', 'GBP'][$i % 3],
                $subtotal - $discount + $tax + $shipping,
                $subtotal,
                $discount,
                $tax,
                $shipping,
                "Address $i, City $i",
                ['standard', 'express'][$i % 2],
                $i % 3 === 0 ? "Note $i" : null,
                $this->date($i),
                $this->date($i + 1),
                $i % 4 !== 0 ? $this->date($i + 1) : null,
            ];
        }, 'orders', ['id', 'customer_id', 'number', 'status', 'currency', 'total', 'subtotal', 'discount', 'tax', 'shipping_cost', 'shipping_address', 'shipping_method', 'note', 'created_at', 'updated_at', 'paid_at']);

        $items = [];
        $payments = [];
        $shipments = [];
        $events = [];
        $itemId = 1;
        $eventId = 1;

        for ($i = 1; $i <= $count; $i++) {
            $itemCount = mt_rand(5, 15);
            for ($k = 0; $k < $itemCount; $k++) {
                $price = $this->money(100, 50000);
                $discount = $this->money(0, 1000);
                $qty = mt_rand(1, 5);
                $items[] = [$itemId++, $i, mt_rand(1, $this->products), $qty, $price, $discount, ($price - $discount) * $qty];
            }

            $payments[] = [
                $i,
                $i,
                $this->money(1000, 500000),
                ['card', 'cash', 'paypal'][$i % 3],
                ['pending', 'paid', 'failed'][$i % 3],
                sprintf('TXN-%010d', $i),
                ['USD', 'EUR', 'GBP'][$i % 3],
                $this->date($i + 1),
            ];

            $shipments[] = [
                $i,
                $i,
                ['UPS', 'FedEx', 'DHL', 'USPS'][$i % 4],
                sprintf('TRK-%010d', $i),
                $this->date($i + 1),
                $i % 3 !== 0 ? $this->date($i + 3) : null,
            ];

            $eventCount = mt_rand(3, 8);
            for ($k = 0; $k < $eventCount; $k++) {
                $events[] = [
                    $eventId++,
                    $i,
                    ['created', 'paid', 'shipped', 'delivered', 'cancelled'][($i + $k) % 5],
                    $k === 0 ? null : "Event $k",
                    $this->date($i + $k),
                ];
            }
        }

        $this->insertBatch('order_items', ['id', 'order_id', 'product_id', 'quantity', 'price', 'discount', 'total'], $items);
        $this->insertBatch('payments', ['id', 'order_id', 'amount', 'method', 'status', 'transaction_id', 'currency', 'paid_at'], $payments);
        $this->insertBatch('shipments', ['id', 'order_id', 'carrier', 'tracking_number', 'shipped_at', 'delivered_at'], $shipments);
        $this->insertBatch('order_events', ['id', 'order_id', 'type', 'note', 'created_at'], $events);
    }

    private function seedComments(): void
    {
        $comments = [];
        $id = 1;
        for ($p = 1; $p <= $this->products; $p++) {
            $n = mt_rand(5, 15);
            for ($k = 0; $k < $n; $k++) {
                $comments[] = [
                    $id,
                    $p,
                    mt_rand(1, $this->customers),
                    "Comment body $id",
                    mt_rand(1, 5),
                    1,
                    mt_rand(0, 100),
                    $this->date($id),
                ];
                $id++;
            }
        }
        $this->insertBatch('comments', ['id', 'product_id', 'customer_id', 'body', 'rating', 'is_approved', 'helpful_count', 'created_at'], $comments);
    }

    private function date(int $i): string
    {
        $day = ($i % 365) + 1;

        return sprintf('2024-%02d-%02d %02d:%02d:%02d', ($day % 12) + 1, ($day % 28) + 1, $i % 24, $i % 60, $i % 60);
    }

    private function money(int $min, int $max): float
    {
        return round(mt_rand($min, $max) / 100, 2);
    }

    private function insertRows(int $count, callable $row, string $table, array $columns): void
    {
        $chunk = [];
        $flush = function () use (&$chunk, $table, $columns): void {
            if ($chunk !== []) {
                $this->insertBatch($table, $columns, $chunk);
                $chunk = [];
            }
        };

        for ($i = 1; $i <= $count; $i++) {
            $chunk[] = $row($i);
            if (count($chunk) >= 500) {
                $flush();
            }
        }
        $flush();
    }

    private function insertBatch(string $table, array $columns, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $rowPlaceholder = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';

        // Chunk internally to stay under SQLite's max-variable limit.
        foreach (array_chunk($rows, 500) as $chunk) {
            $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $columns) . ') VALUES '
                . implode(',', array_fill(0, count($chunk), $rowPlaceholder));

            $stmt = $this->pdo->prepare($sql);
            $values = [];
            foreach ($chunk as $row) {
                foreach ($row as $value) {
                    $values[] = $value;
                }
            }
            $stmt->execute($values);
        }
    }

    private function report(): void
    {
        $tables = ['customers', 'addresses', 'products', 'categories', 'orders', 'order_items', 'payments', 'shipments', 'order_events', 'comments', 'product_category'];
        foreach ($tables as $table) {
            $count = (int)$this->pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            printf("%-16s %s\n", $table, number_format($count));
        }
    }
}
