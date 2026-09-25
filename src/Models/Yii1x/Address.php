<?php

declare(strict_types=1);

namespace Bench\Models\Yii1x;

use Yii1x\ActiveRecord\ActiveRecord;

class Address extends ActiveRecord
{
    public function tableName(): string
    {
        return 'addresses';
    }

    public function relations(): array
    {
        return [
            'customer' => [self::BELONGS_TO, Customer::class, 'customer_id'],
        ];
    }
}
