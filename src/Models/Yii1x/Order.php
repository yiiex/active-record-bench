<?php

declare(strict_types=1);

namespace Bench\Models\Yii1x;

use Yii1x\ActiveRecord\ActiveRecord;

class Order extends ActiveRecord
{
    public function tableName(): string
    {
        return 'orders';
    }

    public function relations(): array
    {
        return [
            'customer' => [self::BELONGS_TO, Customer::class, 'customer_id'],
            'items' => [self::HAS_MANY, OrderItem::class, 'order_id'],
            'payment' => [self::HAS_ONE, Payment::class, 'order_id'],
            'shipment' => [self::HAS_ONE, Shipment::class, 'order_id'],
            'events' => [self::HAS_MANY, OrderEvent::class, 'order_id'],
        ];
    }
}
