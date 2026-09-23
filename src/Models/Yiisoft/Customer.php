<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveQueryInterface;
use Yiisoft\ActiveRecord\ActiveRecord;

class Customer extends ActiveRecord
{
    public ?int $id = null;
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public ?string $country = null;
    public string $status = 'active';
    public int $is_verified = 0;
    public int $points = 0;
    public ?string $notes = null;
    public string $created_at = '';
    public ?string $last_login_at = null;

    public function tableName(): string
    {
        return '{{%customers}}';
    }

    public function relationQuery(string $name): ActiveQueryInterface
    {
        return match ($name) {
            'orders' => $this->hasMany(Order::class, ['customer_id' => 'id']),
            default => parent::relationQuery($name),
        };
    }
}
