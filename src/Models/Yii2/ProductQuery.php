<?php

declare(strict_types=1);

namespace Bench\Models\Yii2;

use yii\db\ActiveQuery;

class ProductQuery extends ActiveQuery
{
    public function active(): static
    {
        return $this->andWhere(['is_active' => 1]);
    }
}
