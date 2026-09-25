<?php

declare(strict_types=1);

namespace Bench\Models\Yii1x;

use Yii1x\ActiveRecord\ActiveRecord;

class Shipment extends ActiveRecord
{
    public function tableName(): string
    {
        return 'shipments';
    }

    public function relations(): array
    {
        return [
            'order' => [self::BELONGS_TO, Order::class, 'order_id'],
        ];
    }
}
