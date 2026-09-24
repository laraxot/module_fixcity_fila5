<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Class Category.
 *
 * Modello per la gestione delle categorie dei ticket.
 * Le categorie sono utilizzate per classificare i tipi di segnalazioni
 * (es. 'strade', 'illuminazione', 'rifiuti', etc.).
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $icon
 * @property string|null $parent_id
 * @property bool $is_active
 * @property int|null $sort_order
 * @property Category|null $parent
 */
class Category extends BaseModel
{
    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     */
    protected $table = 'categories';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'id';

    /**
     * The data type of the auto-incrementing ID.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'icon',
        'parent_id',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the parent category.
     */
    /** @return BelongsTo<Category, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     *
     * @return HasMany<Category, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all tickets for this category.
     *
     * @return HasMany<Ticket, $this>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    /**
     * Scope a query to only include active categories.
     *
     * @param  Builder<Category>  $query
     * @return Builder<Category>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root categories (no parent).
     *
     * @param  Builder<Category>  $query
     * @return Builder<Category>
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the category's full name including parent.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            $parentName = SafeStringCastAction::cast($this->parent->getAttribute('name'));
            $currentName = SafeStringCastAction::cast($this->getAttribute('name'));

            return $parentName.' > '.$currentName;
        }

        return SafeStringCastAction::cast($this->getAttribute('name'));
    }

    /**
     * Check if the category has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get all descendants of this category.
     */
    /** @return HasMany<Category, $this> */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * The attributes that should be cast.
     *
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
