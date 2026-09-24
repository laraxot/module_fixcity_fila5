<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Xot\Models\XotBaseModel;

/**
 * Base model modulo Fixcity: stack Xot (factory, RelationX, Updater).
 */
abstract class BaseModel extends XotBaseModel
{
    use SoftDeletes;

    public $incrementing = true;

    public $timestamps = true;

    protected $connection = 'fixcity';

    /** @var list<string> */
    protected $fillable = ['id'];

    protected $primaryKey = 'id';

    /** @var list<string> */
    protected $hidden = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'updated_by' => 'string',
            'created_by' => 'string',
            'deleted_by' => 'string',
        ];
    }
}
