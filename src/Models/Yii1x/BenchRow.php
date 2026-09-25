<?php

declare(strict_types=1);

namespace Bench\Models\Yii1x;

use Yii1x\ActiveRecord\ActiveRecord;

class BenchRow extends ActiveRecord
{
    public function tableName(): string
    {
        return 'bench_rows';
    }
}
