<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveQuery;

class CustomerQuery extends ActiveQuery
{
    public function active(): static
    {
        return $this->andWhere(['status' => 'active']);
    }
}
