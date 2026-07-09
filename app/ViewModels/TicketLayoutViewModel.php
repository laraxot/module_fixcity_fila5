<?php

declare(strict_types=1);

namespace Modules\Fixcity\ViewModels;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Fixcity\Actions\LoadCityDesignDemoCardsAction;
use Modules\Fixcity\Actions\LoadCityDesignFilterCatalogAction;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\ViewModels\Concerns\BuildsTicketLayoutFilters;
use Modules\Fixcity\ViewModels\Concerns\PresentsTicketLayoutChrome;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

final class TicketLayoutViewModel
{
    use BuildsTicketLayoutFilters;
    use PresentsTicketLayoutChrome;

    private string $ns;

    /** @var array<string, mixed> */
    private array $blockData;

    private string $phoneNumber;

    private TicketFilterViewModel $filterViewModel;

    /** @var Builder<Ticket> */
    private Builder $baseQuery;

    /** @var Builder<Ticket> */
    private Builder $filteredQuery;

    /** @var Collection<int, mixed> */
    private Collection $liveTickets;

    /** @var array<int, string> */
    private array $selectedTypes;

    /** @var array<int, string> */
    private array $selectedStatuses;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(array $data)
    {
        $this->ns = 'fixcity::ticket';
        $this->blockData = $data;
        $this->phoneNumber = SafeStringCastAction::cast($this->blockData['phone'] ?? '05 0505');
        $this->selectedTypes = $this->parseSelectedTypes();
        $this->selectedStatuses = $this->parseSelectedStatuses();
        $this->filterViewModel = new TicketFilterViewModel();
        $this->baseQuery = app(BuildPublicTicketsQueryAction::class)->execute();
        $this->filteredQuery = $this->buildFilteredQuery();
        $this->liveTickets = $this->buildLiveTickets();
    }

    private function t(mixed $value, string $default = ''): string
    {
        if (! is_string($value) || $value === '') {
            return $default;
        }

        $resolved = str_contains($value, '::') ? __($value) : $value;
        if (is_array($resolved)) {
            $resolved = $default;
        }

        return str_replace(':phone', $this->phoneNumber, SafeStringCastAction::cast($resolved));
    }

    /**
     * @return array<int, string>
     */
    private function parseSelectedTypes(): array
    {
        return request()->collect('types')
            ->filter(static fn ($type): bool => is_string($type) && $type !== '')
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function parseSelectedStatuses(): array
    {
        return request()->collect('statuses')
            ->filter(static fn ($status): bool => is_string($status) && $status !== '')
            ->values()
            ->all();
    }

    /** @return Builder<Ticket> */
    private function buildFilteredQuery(): Builder
    {
        $query = clone $this->baseQuery;
        if ($this->selectedTypes !== []) {
            $query->whereIn('type', $this->selectedTypes);
        }

        return $query;
    }

    /** @return Collection<int, mixed> */
    private function buildLiveTickets(): Collection
    {
        if ($this->useCityDesignListDemo()) {
            /** @var Collection<int, mixed> $demoTickets */
            $demoTickets = Collection::make(app(LoadCityDesignDemoCardsAction::class)->execute());

            return $demoTickets;
        }

        $tickets = (clone $this->filteredQuery)
            ->latest()
            ->take(20)
            ->get();

        $minListCards = 3;
        if ($tickets->count() < $minListCards) {
            /** @var array<int, int|string> $excludeIds */
            $excludeIds = $tickets->pluck('id')->all();
            $supplements = $this->filterViewModel->getSupplementListItems(
                $minListCards - $tickets->count(),
                $excludeIds,
            );
            $tickets = $tickets->concat($supplements);
        }

        /** @var Collection<int, mixed> $live */
        $live = Collection::make($tickets->all());

        return $live;
    }

    public function useCityDesignListDemo(): bool
    {
        return (bool) ($this->blockData['use_design_comuni_list_demo'] ?? false);
    }

    public function resolvedLast12MonthsCount(): int
    {
        if ($this->useCityDesignListDemo()) {
            return LoadCityDesignFilterCatalogAction::REFERENCE_RESOLVED_LAST_12_MONTHS;
        }

        return (int) Ticket::query()
            ->where('status', 'resolved')
            ->where('updated_at', '>=', now()->subYear())
            ->count();
    }

    /**
     * @return array{legend: string, items: array<int, array<string, mixed>>, totalCount: int}
     */
    private function cityDesignFilterCatalog(): array
    {
        return app(LoadCityDesignFilterCatalogAction::class)->execute();
    }
}
