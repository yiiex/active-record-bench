<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveQueryInterface;
use Yiisoft\ActiveRecord\ActiveRecord;

class Order extends ActiveRecord
{
    public ?int $id = null;
    public int $customer_id = 0;
    public string $number = '';
    public string $status = 'new';
    public string $currency = 'USD';
    public float $total = 0.0;
    public float $subtotal = 0.0;
    public float $discount = 0.0;
    public float $tax = 0.0;
    public float $shipping_cost = 0.0;
    public ?string $shipping_address = null;
    public ?string $shipping_method = null;
    public ?string $note = null;
    public string $created_at = '';
    public string $updated_at = '';
    public ?string $paid_at = null;

    public function tableName(): string
    {
        return '{{%orders}}';
    }

    public function relationQuery(string $name): ActiveQueryInterface
    {
        return match ($name) {
            'customer' => $this->hasOne(Customer::class, ['id' => 'customer_id']),
            default => parent::relationQuery($name),
        };
    }
}
