<?php

declare(strict_types=1);

namespace Bench\Models\Yii1x;

use Yii1x\ActiveRecord\ActiveRecord;

class OrderItem extends ActiveRecord
{
    public function tableName(): string
    {
        return 'order_items';
    }

    public function relations(): array
    {
        return [
            'order' => [self::BELONGS_TO, Order::class, 'order_id'],
            'product' => [self::BELONGS_TO, Product::class, 'product_id'],
        ];
    }
}
