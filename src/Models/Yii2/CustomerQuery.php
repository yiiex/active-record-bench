<?php

declare(strict_types=1);

namespace Bench\Models\Yii2;

use yii\db\ActiveQuery;

class CustomerQuery extends ActiveQuery
{
    public function active(): static
    {
        return $this->andWhere(['status' => 'active']);
    }
}
