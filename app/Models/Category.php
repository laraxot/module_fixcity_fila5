<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Category.
 *
 * Modello per la gestione delle categorie dei ticket.
 * Le categorie sono utilizzate per classificare i tipi di segnalazioni
 * (es. 'strade', 'illuminazione', 'rifiuti', etc.).
 */
class Category extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
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

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all tickets for this category.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root categories (no parent).
     */
    public function scopeRoot(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the category's full name including parent.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            $parentName = (string) $this->parent->getAttribute('name');
            $currentName = (string) $this->getAttribute('name');

            return $parentName.' > '.$currentName;
        }

        return (string) $this->getAttribute('name');
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
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }
}
