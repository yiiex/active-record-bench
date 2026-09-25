<?php

declare(strict_types=1);

namespace Bench\Models\Yii1x;

use Yii1x\ActiveRecord\ActiveRecord;

class Customer extends ActiveRecord
{
    public function tableName(): string
    {
        return 'customers';
    }

    public function relations(): array
    {
        return [
            'orders' => [self::HAS_MANY, Order::class, 'customer_id'],
            'addresses' => [self::HAS_MANY, Address::class, 'customer_id'],
        ];
    }

    public function active(): static
    {
        $this->getDbCriteria()->compare($this->getTableAlias() . '.status', 'active');

        return $this;
    }
}
