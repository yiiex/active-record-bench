<?php

declare(strict_types=1);

namespace Bench\Models\Yii1x;

use Yii1x\ActiveRecord\ActiveRecord;

class Category extends ActiveRecord
{
    public function tableName(): string
    {
        return 'categories';
    }

    public function relations(): array
    {
        return [
            'parent' => [self::BELONGS_TO, Category::class, 'parent_id'],
            'children' => [self::HAS_MANY, Category::class, 'parent_id'],
            'products' => [self::MANY_MANY, Product::class, 'product_category(category_id, product_id)'],
        ];
    }
}
