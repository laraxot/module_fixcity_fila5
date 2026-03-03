<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Modules\Fixcity\Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static ActivityFactory factory($count = null, $state = [])
 * @method static Builder|Activity newModelQuery()
 * @method static Builder|Activity newQuery()
 * @method static Builder|Activity onlyTrashed()
 * @method static Builder|Activity query()
 * @method static Builder|Activity withTrashed()
 * @method static Builder|Activity withoutTrashed()
 * @property-read Profile|null $creator
 * @property-read Profile|null $updater
 * @property-read \Modules\Fixcity\Models\Profile|null $deleter
 * @mixin \Eloquent
 */
class Activity extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
    ];
}
