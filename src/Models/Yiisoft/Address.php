<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveQueryInterface;
use Yiisoft\ActiveRecord\ActiveRecord;

class Address extends ActiveRecord
{
    public ?int $id = null;
    public int $customer_id = 0;
    public string $country = '';
    public string $city = '';
    public string $street = '';
    public string $zip = '';
    public ?string $phone = null;
    public int $is_default = 0;

    public function tableName(): string
    {
        return '{{%addresses}}';
    }

    public function relationQuery(string $name): ActiveQueryInterface
    {
        return match ($name) {
            'customer' => $this->hasOne(Customer::class, ['id' => 'customer_id']),
            default => parent::relationQuery($name),
        };
    }
}
