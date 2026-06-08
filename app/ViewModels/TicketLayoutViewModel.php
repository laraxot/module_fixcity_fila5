<?php

declare(strict_types=1);

namespace Modules\Fixcity\ViewModels;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Fixcity\Actions\LoadDesignComuniElencoDemoCardsAction;
use Modules\Fixcity\Actions\LoadDesignComuniElencoFilterCatalogAction;
use Modules\Fixcity\Actions\LoadPublicTicketsGeoJsonAction;
use Modules\Fixcity\Models\Ticket;

final class TicketLayoutViewModel
{
    private string $ns;

    private array $blockData;

    private string $phoneNumber;

    private SegnalazioniFilterViewModel $filterViewModel;

    private Builder $baseQuery;

    private Builder $filteredQuery;

    private Collection $liveTickets;

    /** @var array<int, string> */
    private array $selectedTypes;

    /** @var array<int, string> */
    private array $selectedStatuses;

    public function __construct(array $data)
    {
        $this->ns = 'fixcity::ticket';
        $this->blockData = $data;
        $this->phoneNumber = (string) ($this->blockData['phone'] ?? '05 0505');
        $this->selectedTypes = $this->parseSelectedTypes();
        $this->selectedStatuses = $this->parseSelectedStatuses();
        $this->filterViewModel = new SegnalazioniFilterViewModel();
        $this->baseQuery = app(BuildPublicTicketsQueryAction::class)->execute();
        $this->filteredQuery = $this->buildFilteredQuery();
        $this->liveTickets = $this->buildLiveTickets();
    }

    private function t(string $value, string $default = ''): string
    {
        if (empty($value)) {
            return $default;
        }

        $resolved = str_contains($value, '::') ? __($value) : $value;

        return str_replace(':phone', $this->phoneNumber, $resolved);
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

    private function buildFilteredQuery(): Builder
    {
        $query = clone $this->baseQuery;
        if ($this->selectedTypes !== []) {
            $query->whereIn('type', $this->selectedTypes);
        }

        return $query;
    }

    private function buildLiveTickets(): Collection
    {
        if ($this->useDesignComuniListDemo()) {
            return Collection::make(app(LoadDesignComuniElencoDemoCardsAction::class)->execute());
        }

        $tickets = (clone $this->filteredQuery)
            ->latest()
            ->take(20)
            ->get();

        $minListCards = 3;
        if ($tickets->count() < $minListCards) {
            $supplements = $this->filterViewModel->getSupplementListItems(
                $minListCards - $tickets->count(),
                $tickets->pluck('id')->all(),
            );
            $tickets = $tickets->concat($supplements);
        }

        return $tickets;
    }

    public function useDesignComuniListDemo(): bool
    {
        return (bool) ($this->blockData['use_design_comuni_list_demo'] ?? false);
    }

    public function resolvedLast12MonthsCount(): int
    {
        if ($this->useDesignComuniListDemo()) {
            return LoadDesignComuniElencoFilterCatalogAction::REFERENCE_RESOLVED_LAST_12_MONTHS;
        }

        return (int) Ticket::query()
            ->where('status', 'resolved')
            ->where('updated_at', '>=', now()->subYear())
            ->count();
    }

    /**
     * @return array{legend: string, items: array<int, array<string, mixed>>, totalCount: int}
     */
    private function designComuniFilterCatalog(): array
    {
        return app(LoadDesignComuniElencoFilterCatalogAction::class)->execute();
    }

    // ── Navigation ──

    public function breadcrumbItems(): array
    {
        $items = [];
        foreach ($this->blockData['breadcrumb'] ?? [] as $item) {
            $items[] = [
                'label' => $this->t($item['label'] ?? ''),
                'url' => $item['url'] ?? null,
                'active' => $item['active'] ?? false,
            ];
        }

        return $items;
    }

    // ── Heading ──

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
        $key = (! empty($raw) && str_contains((string) $raw, '::')) ? (string) $raw : $this->ns . '.heading.subtitle.text';

        return trans_choice($key, $resolvedCount, ['count' => $resolvedCount]);
    }

    // ── Tabs ──

    public function tabs(): array
    {
        $tabsData = $this->blockData['tabs'] ?? [];
        $rawTabs = $tabsData['items'] ?? [];
        $tabs = [];
        foreach ($rawTabs as $tab) {
            $tabs[] = [
                'id' => $tab['id'] ?? 'map',
                'label' => $this->t($tab['label'] ?? ''),
                'active' => $tab['active'] ?? false,
            ];
        }

        return $tabs;
    }

    public function tabsId(): string
    {
        return $this->blockData['tabs']['id'] ?? 'map-and-list';
    }

    public function defaultActiveTab(): string
    {
        foreach ($this->tabs() as $tab) {
            if ($tab['active'] ?? false) {
                return $tab['id'];
            }
        }

        return $this->tabs()[0]['id'] ?? 'map';
    }

    public function mapTabId(): string
    {
        return $this->tabs()[0]['id'] ?? 'map';
    }

    public function listTabId(): string
    {
        return $this->tabs()[1]['id'] ?? 'list';
    }

    public function defaultPanelId(): string
    {
        $active = $this->defaultActiveTab();
        foreach ($this->tabs() as $index => $tab) {
            if (($tab['id'] ?? '') === $active) {
                return 'data-ex-disservizio' . ($index + 1);
            }
        }

        return 'data-ex-disservizio1';
    }

    // ── Filters ──

    public function filtersTitle(): string
    {
        $fromCms = $this->blockData['main_content']['filters']['title'] ?? '';
        if ($fromCms !== '' && ! str_contains((string) $fromCms, '::')) {
            return (string) $fromCms;
        }

        if ($this->useDesignComuniListDemo()) {
            return strtoupper($this->designComuniFilterCatalog()['legend']);
        }

        return $this->filterViewModel->getCatalogLegend();
    }

    /** @return array<int, array<string, mixed>> */
    public function filterItems(): array
    {
        if ($this->useDesignComuniListDemo()) {
            return $this->designComuniFilterCatalog()['items'];
        }

        return $this->filterViewModel->getFilterItems();
    }

    public function resultsCount(): int
    {
        if ($this->useDesignComuniListDemo()) {
            $catalog = $this->designComuniFilterCatalog();
            if ($this->selectedTypes === []) {
                return $catalog['totalCount'];
            }

            $sum = 0;
            foreach ($catalog['items'] as $item) {
                $value = (string) ($item['value'] ?? '');
                if ($value !== '' && in_array($value, $this->selectedTypes, true)) {
                    $sum += (int) ($item['count'] ?? 0);
                }
            }

            return $sum > 0 ? $sum : $catalog['totalCount'];
        }

        return $this->filterViewModel->getFilteredCount($this->selectedTypes, $this->selectedStatuses);
    }

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
        if ($this->useDesignComuniListDemo()) {
            return 'STATO';
        }

        return $this->filterViewModel->getStatusCatalogLegend();
    }

    /** @return array<int, array<string, mixed>> */
    public function statusFilterItems(): array
    {
        if ($this->useDesignComuniListDemo()) {
            return [];
        }

        return $this->filterViewModel->getStatusFilterItems();
    }

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
        return $this->useDesignComuniListDemo();
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

    // ── Map ──

    public function mapDataUrl(): string
    {
        return app(LoadPublicTicketsGeoJsonAction::class)->publicUrl();
    }

    // ── CTA ──

    public function cta(): array
    {
        $mainContent = $this->blockData['main_content'] ?? [];
        $rawCta = $mainContent['cta'] ?? [];
        if ($rawCta === []) {
            return [];
        }

        return [
            'title' => $this->t($rawCta['title'] ?? '', __($this->ns . '.map.cta.title.label')),
            'text' => $this->t($rawCta['text'] ?? '', __($this->ns . '.map.cta.text.label')),
            'button_text' => $this->t($rawCta['button_text'] ?? '', __($this->ns . '.map.cta.button.label')),
            'button_url' => $rawCta['button_url'] ?? '/it/segnalazione-crea',
        ];
    }

    // ── Contacts ──

    public function contacts(): array
    {
        $contactsData = $this->blockData['contacts'] ?? [];

        /** @var array<int, array<string, mixed>> $rawItems */
        $rawItems = $contactsData['contacts'] ?? [];
        if (count($rawItems) < 4) {
            $rawItems = $this->defaultContactItems();
        }

        return [
            'title' => $this->t(
                $contactsData['contact_title'] ?? '',
                __($this->ns . '.contacts.block_title.label'),
            ),
            'items' => collect($rawItems)->map(
                fn ($contact) => [
                    'label' => $this->t($contact['label'] ?? ''),
                    'url' => $contact['url'] ?? '#',
                    'icon' => $contact['icon'] ?? 'it-help-circle',
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
        return $this->blockData['contacts']['id'] ?? 'info-contacts';
    }

    // ── Tickets ──

    public function liveTickets(): Collection
    {
        return $this->liveTickets;
    }

    // ── Misc ──

    public function sprite(): string
    {
        return '/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg';
    }

    public function mainContentId(): string
    {
        return ($this->blockData['main_content']['id'] ?? 'filter-and-cards');
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
