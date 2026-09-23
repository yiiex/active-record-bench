<?php

declare(strict_types=1);

namespace Bench\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    public $timestamps = false;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
