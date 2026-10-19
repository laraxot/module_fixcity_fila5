<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

class Report extends BaseModel
{
    protected $table = 'reports';

    protected $fillable = ['title', 'description', 'location', 'address', 'category', 'status', 'metadata'];

    protected $casts = ['location' => 'array', 'metadata' => 'array'];
}
