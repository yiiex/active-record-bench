<?php

declare(strict_types=1);

namespace Bench\Models\Yii1x;

use Yii1x\ActiveRecord\ActiveRecord;

class OrderEvent extends ActiveRecord
{
    public function tableName(): string
    {
        return 'order_events';
    }

    public function relations(): array
    {
        return [
            'order' => [self::BELONGS_TO, Order::class, 'order_id'],
        ];
    }
}
