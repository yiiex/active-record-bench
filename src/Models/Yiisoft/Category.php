<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveQueryInterface;
use Yiisoft\ActiveRecord\ActiveRecord;

class Category extends ActiveRecord
{
    public ?int $id = null;
    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public ?int $parent_id = null;
    public int $position = 0;
    public int $is_active = 1;

    public function tableName(): string
    {
        return '{{%categories}}';
    }

    public function relationQuery(string $name): ActiveQueryInterface
    {
        return match ($name) {
            'parent' => $this->hasOne(Category::class, ['id' => 'parent_id']),
            'children' => $this->hasMany(Category::class, ['parent_id' => 'id']),
            'products' => $this->hasMany(Product::class, ['id' => 'product_id'])
                ->viaTable('product_category', ['category_id' => 'id']),
            default => parent::relationQuery($name),
        };
    }
}
