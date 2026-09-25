<?php

declare(strict_types=1);

namespace Bench\Models\Yii2;

use yii\db\ActiveRecord;

class Customer extends ActiveRecord
{
    public static function tableName()
    {
        return 'customers';
    }

    public static function find()
    {
        return new CustomerQuery(static::class);
    }

    public function getOrders()
    {
        return $this->hasMany(Order::class, ['customer_id' => 'id']);
    }

    public function getAddresses()
    {
        return $this->hasMany(Address::class, ['customer_id' => 'id']);
    }
}
