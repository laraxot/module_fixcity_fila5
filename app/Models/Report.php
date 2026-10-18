<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    protected $fillable = ['title', 'description', 'location', 'address', 'category', 'status', 'metadata'];
    protected $casts = ['location' => 'array', 'metadata' => 'array'];
}
