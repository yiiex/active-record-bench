<?php

declare(strict_types=1);

namespace Bench\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public $timestamps = false;

    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category', 'product_id', 'category_id');
    }
}
