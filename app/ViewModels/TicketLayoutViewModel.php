<?php

declare(strict_types=1);

namespace Modules\Fixcity\ViewModels;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Fixcity\Actions\LoadCityDesignDemoCardsAction;
use Modules\Fixcity\ViewModels\TicketFilterViewModel;
use Modules\Fixcity\Actions\LoadCityDesignFilterCatalogAction;
use Modules\Fixcity\Actions\LoadPublicTicketsGeoJsonAction;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

final class TicketLayoutViewModel
{
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

    /**
     * @return array<int, array{label: string, url: string|null, active: bool}>
     */
    public function breadcrumbItems(): array
    {
        $items = [];
        /** @var array<int, array<string, mixed>> $rawItems */
        $rawItems = is_array($this->blockData['breadcrumb'] ?? null) ? $this->blockData['breadcrumb'] : [];
        foreach ($rawItems as $item) {
            $items[] = [
                'label' => $this->t($item['label'] ?? ''),
                'url' => isset($item['url']) ? SafeStringCastAction::cast($item['url']) : null,
                'active' => (bool) ($item['active'] ?? false),
            ];
        }

        return $items;
    }

    public function title(): string
    {
        return $this->t(
            $this->blockData['title'] ?? '',
            __($this->ns . '.heading.title.label'),
        );
    }

    public function subtitle(): string
    {
        $resolvedCount = Ticket::where('status', 'resolved')
            ->where('updated_at', '>=', now()->subYear())
            ->count();

        $raw = $this->blockData['subtitle'] ?? '';
        $key = (is_string($raw) && $raw !== '' && str_contains($raw, '::')) ? $raw : $this->ns . '.heading.subtitle.text';

        return trans_choice($key, $resolvedCount, ['count' => $resolvedCount]);
    }

    /**
     * @return array<int, array{id: string, label: string, active: bool}>
     */
    public function tabs(): array
    {
        $tabsData = is_array($this->blockData['tabs'] ?? null) ? $this->blockData['tabs'] : [];
        /** @var array<int, array<string, mixed>> $rawTabs */
        $rawTabs = is_array($tabsData['items'] ?? null) ? $tabsData['items'] : [];
        $tabs = [];
        foreach ($rawTabs as $tab) {
            $tabs[] = [
                'id' => SafeStringCastAction::cast($tab['id'] ?? 'map'),
                'label' => $this->t($tab['label'] ?? ''),
                'active' => (bool) ($tab['active'] ?? false),
            ];
        }

        return $tabs;
    }

    public function tabsId(): string
    {
        $tabs = is_array($this->blockData['tabs'] ?? null) ? $this->blockData['tabs'] : [];

        return SafeStringCastAction::cast($tabs['id'] ?? 'map-and-list');
    }

    public function defaultActiveTab(): string
    {
        foreach ($this->tabs() as $tab) {
            if ($tab['active']) {
                return $tab['id'];
            }
        }

        $tabs = $this->tabs();

        return $tabs[0]['id'] ?? 'map';
    }

    public function mapTabId(): string
    {
        $tabs = $this->tabs();

        return $tabs[0]['id'] ?? 'map';
    }

    public function listTabId(): string
    {
        $tabs = $this->tabs();

        return $tabs[1]['id'] ?? 'list';
    }

    public function defaultPanelId(): string
    {
        $active = $this->defaultActiveTab();
        foreach ($this->tabs() as $index => $tab) {
            if ($tab['id'] === $active) {
                return 'data-ex-disservizio' . ($index + 1);
            }
        }

        return 'data-ex-disservizio1';
    }

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

    public function useReferenceStaticMap(): bool
    {
        return $this->useCityDesignListDemo();
    }

    public function referenceMapImageUrl(): string
    {
        return '/themes/Sixteen/design-comuni/assets/images/map-placeholder.svg';
    }

    /** @return array<int, string> */
    public function selectedTypes(): array
    {
        return $this->selectedTypes;
    }

    public function filterItemsAttr(): string
    {
        return e(json_encode($this->filterItems(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    public function selectedTypesAttr(): string
    {
        return e(json_encode($this->selectedTypes, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    public function mapDataUrl(): string
    {
        return app(LoadPublicTicketsGeoJsonAction::class)->publicUrl();
    }

    /**
     * @return array<string, string>|array{}
     */
    public function cta(): array
    {
        $mainContent = is_array($this->blockData['main_content'] ?? null) ? $this->blockData['main_content'] : [];
        $rawCta = is_array($mainContent['cta'] ?? null) ? $mainContent['cta'] : [];
        if ($rawCta === []) {
            return [];
        }

        return [
            'title' => $this->t($rawCta['title'] ?? '', __($this->ns . '.map.cta.title.label')),
            'text' => $this->t($rawCta['text'] ?? '', __($this->ns . '.map.cta.text.label')),
            'button_text' => $this->t($rawCta['button_text'] ?? '', __($this->ns . '.map.cta.button.label')),
            'button_url' => SafeStringCastAction::cast($rawCta['button_url'] ?? '/it/tests/ticket-crea'),
        ];
    }

    /**
     * @return array{title: string, items: array<int, array{label: string, url: string, icon: string}>}
     */
    public function contacts(): array
    {
        $contactsData = is_array($this->blockData['contacts'] ?? null) ? $this->blockData['contacts'] : [];

        /** @var array<int, array<string, mixed>> $rawItems */
        $rawItems = is_array($contactsData['contacts'] ?? null) ? $contactsData['contacts'] : [];
        if (count($rawItems) < 4) {
            $rawItems = $this->defaultContactItems();
        }

        return [
            'title' => $this->t(
                $contactsData['contact_title'] ?? '',
                __($this->ns . '.contacts.block_title.label'),
            ),
            'items' => collect($rawItems)->map(
                fn (array $contact): array => [
                    'label' => $this->t($contact['label'] ?? ''),
                    'url' => SafeStringCastAction::cast($contact['url'] ?? '#'),
                    'icon' => SafeStringCastAction::cast($contact['icon'] ?? 'it-help-circle'),
                ],
            )->all(),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function defaultContactItems(): array
    {
        return [
            [
                'label' => $this->ns . '.contacts.faq.link.label',
                'url' => '#',
                'icon' => 'it-help-circle',
            ],
            [
                'label' => $this->ns . '.contacts.assistenza.link.label',
                'url' => '#',
                'icon' => 'it-mail',
            ],
            [
                'label' => $this->ns . '.contacts.phone.link.label',
                'url' => 'tel:' . str_replace(' ', '', $this->phoneNumber),
                'icon' => 'it-phone',
            ],
            [
                'label' => $this->ns . '.contacts.appointment.link.label',
                'url' => '#',
                'icon' => 'it-calendar',
            ],
        ];
    }

    public function contactsId(): string
    {
        $contacts = is_array($this->blockData['contacts'] ?? null) ? $this->blockData['contacts'] : [];

        return SafeStringCastAction::cast($contacts['id'] ?? 'info-contacts');
    }

    /** @return Collection<int, mixed> */
    public function liveTickets(): Collection
    {
        return $this->liveTickets;
    }

    public function sprite(): string
    {
        return '/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg';
    }

    public function mainContentId(): string
    {
        $mainContent = is_array($this->blockData['main_content'] ?? null) ? $this->blockData['main_content'] : [];

        return SafeStringCastAction::cast($mainContent['id'] ?? 'filter-and-cards');
    }

    public function filtersSectionId(): string
    {
        return $this->mainContentId();
    }

    public function phoneNumber(): string
    {
        return $this->phoneNumber;
    }
}
