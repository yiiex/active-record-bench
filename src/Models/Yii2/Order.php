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

    public function getItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::class, ['order_id' => 'id']);
    }

    public function getShipment()
    {
        return $this->hasOne(Shipment::class, ['order_id' => 'id']);
    }

    public function getEvents()
    {
        return $this->hasMany(OrderEvent::class, ['order_id' => 'id']);
    }
}
