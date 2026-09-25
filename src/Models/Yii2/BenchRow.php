<?php

declare(strict_types=1);

namespace Bench\Models\Yii2;

use yii\db\ActiveRecord;

class BenchRow extends ActiveRecord
{
    public static function tableName()
    {
        return 'bench_rows';
    }
}
