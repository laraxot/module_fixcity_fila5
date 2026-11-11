<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Fixcity\Models\Category;
use Spatie\QueueableAction\QueueableAction;

final class ListPublicCategoriesAction
{
    use QueueableAction;

    /** @return Collection<int, Category> */
    public function execute(): Collection
    {
        return Category::query()
            ->active()
            ->root()
            ->with(['children' => static fn (HasMany $query): HasMany => $query->active()->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
