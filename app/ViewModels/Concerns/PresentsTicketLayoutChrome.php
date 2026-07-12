<?php

declare(strict_types=1);

namespace Modules\Fixcity\ViewModels\Concerns;

use Illuminate\Support\Collection;
use Modules\Fixcity\Actions\LoadPublicTicketsGeoJsonAction;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Chrome FO pagina elenco segnalazioni (breadcrumb, tab, CTA, contatti).
 *
 * @property-read string $ns
 * @property-read array<string, mixed> $blockData
 * @property-read string $phoneNumber
 * @property-read list<string> $selectedTypes
 * @property-read Collection<int, Ticket|object> $liveTickets
 */
trait PresentsTicketLayoutChrome
{
    /**
     * @return list<array{label: string, url: string|null, active: bool}>
     */
    public function breadcrumbItems(): array
    {
        $items = [];
        /** @var array<int, array<string, mixed>> $rawItems */
        $rawItems = is_array($this->blockData['breadcrumb'] ?? null) ? $this->blockData['breadcrumb'] : [];
        foreach ($rawItems as $item) {
            if (! is_array($item)) {
                continue;
            }
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
            __($this->ns.'.heading.title.label'),
        );
    }

    public function subtitle(): string
    {
        $resolvedCount = Ticket::where('status', 'resolved')
            ->where('updated_at', '>=', now()->subYear())
            ->count();

        $raw = $this->blockData['subtitle'] ?? '';
        $key = (is_string($raw) && $raw !== '' && str_contains($raw, '::')) ? $raw : $this->ns.'.heading.subtitle.text';

        return trans_choice($key, $resolvedCount, ['count' => $resolvedCount]);
    }

    /**
     * @return list<array{id: string, label: string, active: bool}>
     */
    public function tabs(): array
    {
        $tabsData = is_array($this->blockData['tabs'] ?? null) ? $this->blockData['tabs'] : [];
        /** @var array<int, array<string, mixed>> $rawTabs */
        $rawTabs = is_array($tabsData['items'] ?? null) ? $tabsData['items'] : [];
        $tabs = [];
        foreach ($rawTabs as $tab) {
            if (! is_array($tab)) {
                continue;
            }
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
        if ($tabs === []) {
            return 'map';
        }

        return $tabs[0]['id'];
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
                return 'data-ex-disservizio'.($index + 1);
            }
        }

        return 'data-ex-disservizio1';
    }

    public function useReferenceStaticMap(): bool
    {
        return $this->useCityDesignListDemo();
    }

    public function referenceMapImageUrl(): string
    {
        return '/themes/Sixteen/design-comuni/assets/images/map-placeholder.svg';
    }

    /** @return list<string> */
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
        return LoadPublicTicketsGeoJsonAction::PUBLIC_URL;
    }

    /**
     * @return array{title: string, text: string, button_text: string, button_url: string}|array{}
     */
    public function cta(): array
    {
        $mainContent = is_array($this->blockData['main_content'] ?? null) ? $this->blockData['main_content'] : [];
        $rawCta = is_array($mainContent['cta'] ?? null) ? $mainContent['cta'] : [];
        if ($rawCta === []) {
            return [];
        }

        return [
            'title' => $this->t($rawCta['title'] ?? '', __($this->ns.'.map.cta.title.label')),
            'text' => $this->t($rawCta['text'] ?? '', __($this->ns.'.map.cta.text.label')),
            'button_text' => $this->t($rawCta['button_text'] ?? '', __($this->ns.'.map.cta.button.label')),
            'button_url' => SafeStringCastAction::cast($rawCta['button_url'] ?? '/it/tests/ticket-crea'),
        ];
    }

    /**
     * @return array{title: string, items: list<array{label: string, url: string, icon: string}>}
     */
    public function contacts(): array
    {
        $contactsData = is_array($this->blockData['contacts'] ?? null) ? $this->blockData['contacts'] : [];

        /** @var array<int, array<string, mixed>> $rawItems */
        $rawItems = is_array($contactsData['contacts'] ?? null) ? $contactsData['contacts'] : [];
        if (count($rawItems) < 4) {
            $rawItems = $this->defaultContactItems();
        }

        $items = [];
        foreach ($rawItems as $contact) {
            if (! is_array($contact)) {
                continue;
            }
            $items[] = [
                'label' => $this->t($contact['label'] ?? ''),
                'url' => SafeStringCastAction::cast($contact['url'] ?? '#'),
                'icon' => SafeStringCastAction::cast($contact['icon'] ?? 'it-help-circle'),
            ];
        }

        return [
            'title' => $this->t(
                $contactsData['contact_title'] ?? '',
                __($this->ns.'.contacts.block_title.label'),
            ),
            'items' => $items,
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function defaultContactItems(): array
    {
        return [
            [
                'label' => $this->ns.'.contacts.faq.link.label',
                'url' => '#',
                'icon' => 'it-help-circle',
            ],
            [
                'label' => $this->ns.'.contacts.assistenza.link.label',
                'url' => '#',
                'icon' => 'it-mail',
            ],
            [
                'label' => $this->ns.'.contacts.phone.link.label',
                'url' => 'tel:'.str_replace(' ', '', $this->phoneNumber),
                'icon' => 'it-phone',
            ],
            [
                'label' => $this->ns.'.contacts.appointment.link.label',
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

    /** @return Collection<int, Ticket|object> */
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
