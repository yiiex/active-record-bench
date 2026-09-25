<?php

declare(strict_types=1);

namespace Bench\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class BenchRow extends Model
{
    protected $table = 'bench_rows';

    public $timestamps = false;

    protected $guarded = [];
}
