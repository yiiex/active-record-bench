<?php

declare(strict_types=1);

namespace Bench\Models\Yii2;

use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    public static function tableName()
    {
        return 'products';
    }

    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('product_category', ['product_id' => 'id']);
    }
}
