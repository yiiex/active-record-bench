<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveQueryInterface;
use Yiisoft\ActiveRecord\ActiveRecord;
use Yiisoft\ActiveRecord\ActiveRecordInterface;

class Product extends ActiveRecord
{
    public ?int $id = null;
    public string $name = '';
    public string $slug = '';
    public string $sku = '';
    public ?string $brand = null;
    public ?string $description = null;
    public float $price = 0.0;
    public ?float $cost_price = null;
    public int $stock = 0;
    public ?float $weight = null;
    public int $is_active = 1;
    public float $tax_rate = 0.0;
    public float $discount = 0.0;
    public float $rating = 0.0;
    public int $views = 0;
    public int $sold_count = 0;
    public string $created_at = '';
    public string $updated_at = '';

    public function tableName(): string
    {
        return '{{%products}}';
    }

    public static function query(ActiveRecordInterface|string|null $modelClass = null): ActiveQueryInterface
    {
        return new ProductQuery($modelClass ?? static::class);
    }

    public function relationQuery(string $name): ActiveQueryInterface
    {
        return match ($name) {
            'categories' => $this->hasMany(Category::class, ['id' => 'category_id'])
                ->viaTable('product_category', ['product_id' => 'id']),
            default => parent::relationQuery($name),
        };
    }
}
