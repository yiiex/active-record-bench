<?php

declare(strict_types=1);

namespace Bench\Models\Yiisoft;

use Yiisoft\ActiveRecord\ActiveRecord;

class BenchRow extends ActiveRecord
{
    public ?int $id = null;
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public ?string $country = null;
    public string $created_at = '';

    public function tableName(): string
    {
        return '{{%bench_rows}}';
    }
}
