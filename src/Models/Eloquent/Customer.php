<?php

declare(strict_types=1);

namespace Bench\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    public $timestamps = false;

    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
