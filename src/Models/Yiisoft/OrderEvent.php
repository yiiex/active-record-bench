<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveQueryInterface;
use Yiisoft\ActiveRecord\ActiveRecord;

class OrderEvent extends ActiveRecord
{
    public ?int $id = null;
    public int $order_id = 0;
    public string $type = '';
    public ?string $note = null;
    public string $created_at = '';

    public function tableName(): string
    {
        return '{{%order_events}}';
    }

    public function relationQuery(string $name): ActiveQueryInterface
    {
        return match ($name) {
            'order' => $this->hasOne(Order::class, ['id' => 'order_id']),
            default => parent::relationQuery($name),
        };
    }
}
