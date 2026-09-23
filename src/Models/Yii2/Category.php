<?php

declare(strict_types=1);

namespace Bench\Models\Yii2;

use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
    public static function tableName()
    {
        return 'categories';
    }

    public function getParent()
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    public function getChildren()
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id']);
    }

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable('product_category', ['category_id' => 'id']);
    }
}
