<?php

declare(strict_types=1);

namespace Modules\Fixcity\ViewModels\Concerns;

use Modules\Fixcity\Actions\LoadCityDesignFilterCatalogAction;
use Modules\Fixcity\Actions\LoadPublicTicketsGeoJsonAction;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

trait BuildsTicketLayoutFilters
{
    public function filtersTitle(): string
    {
        $mainContent = is_array($this->blockData['main_content'] ?? null) ? $this->blockData['main_content'] : [];
        $filters = is_array($mainContent['filters'] ?? null) ? $mainContent['filters'] : [];
        $fromCms = SafeStringCastAction::cast($filters['title'] ?? '');
        if ($fromCms !== '' && ! str_contains($fromCms, '::')) {
            return $fromCms;
        }

        if ($this->useCityDesignListDemo()) {
            return strtoupper($this->cityDesignFilterCatalog()['legend']);
        }

        return $this->filterViewModel->getCatalogLegend();
    }

    /** @return array<int, array<string, mixed>> */
    public function filterItems(): array
    {
        if ($this->useCityDesignListDemo()) {
            return $this->cityDesignFilterCatalog()['items'];
        }

        return $this->filterViewModel->getFilterItems();
    }

    public function resultsCount(): int
    {
        if ($this->useCityDesignListDemo()) {
            $catalog = $this->cityDesignFilterCatalog();
            if ($this->selectedTypes === []) {
                return $catalog['totalCount'];
            }

            $sum = 0;
            foreach ($catalog['items'] as $item) {
                $value = SafeStringCastAction::cast($item['value'] ?? '');
                if ($value !== '' && in_array($value, $this->selectedTypes, true)) {
                    $sum += SafeIntCastAction::cast($item['count'] ?? 0);
                }
            }

            return $sum > 0 ? $sum : $catalog['totalCount'];
        }

        return $this->filterViewModel->getFilteredCount($this->selectedTypes, $this->selectedStatuses);
    }

    /**
     * @return array{title: string, items: array<int, array<string, mixed>>, total: int}
     */
    public function filters(): array
    {
        return [
            'title' => $this->filtersTitle(),
            'items' => $this->filterItems(),
            'total' => $this->resultsCount(),
        ];
    }

    public function statusFiltersTitle(): string
    {
        if ($this->useCityDesignListDemo()) {
            return 'STATUS';
        }

        return $this->filterViewModel->getStatusCatalogLegend();
    }

    /** @return array<int, array<string, mixed>> */
    public function statusFilterItems(): array
    {
        if ($this->useCityDesignListDemo()) {
            return [];
        }

        return $this->filterViewModel->getStatusFilterItems();
    }

    /**
     * @return array{title: string, items: array<int, array<string, mixed>>}
     */
    public function statusFilters(): array
    {
        return [
            'title' => $this->statusFiltersTitle(),
            'items' => $this->statusFilterItems(),
        ];
    }

    public function hasSidebarFilters(): bool
    {
        return $this->filterItems() !== [] || $this->statusFilterItems() !== [];
    }

    /** @return array<int, string> */
    public function selectedStatuses(): array
    {
        return $this->selectedStatuses;
    }
}
