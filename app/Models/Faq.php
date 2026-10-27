<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Domanda frequente del cittadino su segnalazioni e pratiche.
 *
 * @property string $id
 * @property string $locale
 * @property string $question
 * @property string $answer
 * @property int $sort_order
 * @property bool $is_published
 */
class Faq extends BaseModel
{
    public $incrementing = false;

    protected $table = 'faqs';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    /** @var list<string> */
    protected $fillable = [
        'id',
        'locale',
        'question',
        'answer',
        'sort_order',
        'is_published',
    ];

    /**
     * @param  Builder<Faq>  $query
     * @return Builder<Faq>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * @param  Builder<Faq>  $query
     * @return Builder<Faq>
     */
    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ]);
    }
}
