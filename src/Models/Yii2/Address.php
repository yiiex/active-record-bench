<?php

declare(strict_types=1);

namespace Bench\Models\Yii2;

use yii\db\ActiveRecord;

class Address extends ActiveRecord
{
    public static function tableName()
    {
        return 'addresses';
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}
