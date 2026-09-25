<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveQueryInterface;
use Yiisoft\ActiveRecord\ActiveRecord;

class Shipment extends ActiveRecord
{
    public ?int $id = null;
    public int $order_id = 0;
    public string $carrier = '';
    public string $tracking_number = '';
    public string $shipped_at = '';
    public ?string $delivered_at = null;

    public function tableName(): string
    {
        return '{{%shipments}}';
    }

    public function relationQuery(string $name): ActiveQueryInterface
    {
        return match ($name) {
            'order' => $this->hasOne(Order::class, ['id' => 'order_id']),
            default => parent::relationQuery($name),
        };
    }
}
