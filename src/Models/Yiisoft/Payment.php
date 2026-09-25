<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveQueryInterface;
use Yiisoft\ActiveRecord\ActiveRecord;

class Payment extends ActiveRecord
{
    public ?int $id = null;
    public int $order_id = 0;
    public float $amount = 0.0;
    public string $method = '';
    public string $status = 'pending';
    public ?string $transaction_id = null;
    public string $currency = 'USD';
    public string $paid_at = '';

    public function tableName(): string
    {
        return '{{%payments}}';
    }

    public function relationQuery(string $name): ActiveQueryInterface
    {
        return match ($name) {
            'order' => $this->hasOne(Order::class, ['id' => 'order_id']),
            default => parent::relationQuery($name),
        };
    }
}
