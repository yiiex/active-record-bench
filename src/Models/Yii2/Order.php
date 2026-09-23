<?php

declare(strict_types=1);

namespace Bench\Models\Yii2;

use yii\db\ActiveRecord;

class Order extends ActiveRecord
{
    public static function tableName()
    {
        return 'orders';
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}
