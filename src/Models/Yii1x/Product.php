<?php

declare(strict_types=1);

namespace Bench\Models\Yii1x;

use Yii1x\ActiveRecord\ActiveRecord;

class Product extends ActiveRecord
{
    public function tableName(): string
    {
        return 'products';
    }

    public function relations(): array
    {
        return [
            'categories' => [self::MANY_MANY, Category::class, 'product_category(product_id, category_id)'],
        ];
    }
}
