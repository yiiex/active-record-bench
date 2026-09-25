<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveQueryInterface;
use Yiisoft\ActiveRecord\ActiveRecord;

class OrderItem extends ActiveRecord
{
    public ?int $id = null;
    public int $order_id = 0;
    public int $product_id = 0;
    public int $quantity = 1;
    public float $price = 0.0;
    public float $discount = 0.0;
    public float $total = 0.0;

    public function tableName(): string
    {
        return '{{%order_items}}';
    }

    public function relationQuery(string $name): ActiveQueryInterface
    {
        return match ($name) {
            'order' => $this->hasOne(Order::class, ['id' => 'order_id']),
            'product' => $this->hasOne(Product::class, ['id' => 'product_id']),
            default => parent::relationQuery($name),
        };
    }
}
