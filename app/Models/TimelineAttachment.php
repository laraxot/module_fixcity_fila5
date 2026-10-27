<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $path
 * @property string $original_name
 * @property string $mime_type
 * @property string $uploaded_by
 * @property Carbon|null $uploaded_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|TimelineAttachment newModelQuery()
 * @method static Builder|TimelineAttachment newQuery()
 * @method static Builder|TimelineAttachment onlyTrashed()
 * @method static Builder|TimelineAttachment query()
 * @method static Builder|TimelineAttachment wherePath($value)
 * @method static Builder|TimelineAttachment whereOriginalName($value)
 * @method static Builder|TimelineAttachment whereMimeType($value)
 * @method static Builder|TimelineAttachment whereUploadedBy($value)
 * @method static Builder|TimelineAttachment whereUploadedAt($value)
 * @method static Builder|TimelineAttachment whereCreatedAt($value)
 * @method static Builder|TimelineAttachment whereUpdatedAt($value)
 * @method static Builder|TimelineAttachment withTrashed()
 * @method static Builder|TimelineAttachment withoutTrashed()
 *
 * @mixin \Eloquent
 */
class TimelineAttachment extends BaseModel
{
    protected $fillable = [
        'path', 'original_name', 'mime_type', 'uploaded_by', 'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];
}
