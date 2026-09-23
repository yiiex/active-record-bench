<?php

declare(strict_types=1);

namespace Bench\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    public $timestamps = false;

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category', 'category_id', 'product_id');
    }
}
