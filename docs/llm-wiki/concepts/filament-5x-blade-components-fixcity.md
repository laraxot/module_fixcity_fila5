---
title: "Filament 5.x Blade Components in Fixcity — Regola Filament-First"
type: concept
sources: ["https://filamentphp.com/docs/5.x/components/overview"]
confidence: high
created: 2026-05-29
updated: 2026-05-29
tags: [filament, blade-components, filament-first, fixcity, frontoffice]
related:
  - concepts/filament-wizard-architecture.md
  - concepts/filament-first-rule.md
  - ../../Themes/Sixteen/docs/wiki/concepts/filament-5x-blade-components-reference.md
  - ../../../docs/wiki/rules/filament-first-rule.md
---

# Filament 5.x Blade Components in Fixcity — Regola Filament-First

## REGOLA

> **Se Filament ha un componente Blade per il bisogno, usare QUELLO. Mai creare HTML custom equivalente.**

Riferimento completo con API: `laravel/Themes/Sixteen/docs/wiki/concepts/filament-5x-blade-components-reference.md`

---

## Componenti Blade Filament 5.x disponibili

```
x-filament::tabs          → tab navigation (NO ul.nav.nav-tabs Bootstrap)
x-filament::modal         → dialogo/modale (NO data-bs-toggle="modal")
x-filament::dropdown      → menu dropdown (NO data-bs-toggle="dropdown")
x-filament::badge         → badge numerico/label (NO span.badge Bootstrap)
x-filament::button        → pulsante (NO button.btn.btn-primary Bootstrap)
x-filament::breadcrumbs   → breadcrumb (NO ol.breadcrumb Bootstrap)
x-filament::callout       → alert/avviso (NO div.alert Bootstrap)
x-filament::section       → card con header (NO div.card Bootstrap)
x-filament::pagination    → paginazione (NO $collection->links() default)
x-filament::icon-button   → pulsante solo icona
x-filament::loading-indicator → spinner
x-filament::empty-state   → stato vuoto
```

---

## Applicazione al Modulo Fixcity

### Ticket detail page (`/it/segnalazione/{id}`)

```blade
{{-- Status badge --}}
<x-filament::badge :color="$ticket->status->getColor()">
    {{ $ticket->status->getLabel() }}
</x-filament::badge>

{{-- Priority badge --}}
<x-filament::badge :color="$ticket->priority->getColor()">
    {{ $ticket->priority->getLabel() }}
</x-filament::badge>

{{-- Callout informativo --}}
<x-filament::callout icon="heroicon-o-information-circle" color="info">
    <x-slot name="heading">{{ __('fixcity::ticket.status.notice.heading') }}</x-slot>
    <x-slot name="description">{{ __('fixcity::ticket.status.notice.body') }}</x-slot>
</x-filament::callout>
```

### Segnalazioni elenco (`/it`)

```blade
{{-- Tab Mappa/Elenco — IMPLEMENTATO STORY-068 --}}
<x-filament::tabs :label="__('fixcity::segnalazione.tabs.aria.label')">
    <x-filament::tabs.item
        alpine-active="$store.segnalazioniTabs.active === 'map'"
        x-on:click="$store.segnalazioniTabs.setTab('map')"
    >
        {{ __('fixcity::segnalazione.tabs.map') }}
    </x-filament::tabs.item>
    <x-filament::tabs.item
        alpine-active="$store.segnalazioniTabs.active === 'list'"
        x-on:click="$store.segnalazioniTabs.setTab('list')"
    >
        {{ __('fixcity::segnalazione.tabs.list') }}
    </x-filament::tabs.item>
</x-filament::tabs>

{{-- Empty state (nessuna segnalazione) --}}
<x-filament::empty-state
    icon="heroicon-o-map-pin"
    heading="Nessuna segnalazione"
    description="Non ci sono segnalazioni in questa area."
/>

{{-- Pagination --}}
<x-filament::pagination :paginator="$tickets" />
```

### Modal filtri mobile (backlog)

```blade
{{-- Trigger --}}
<x-filament::icon-button
    icon="heroicon-o-funnel"
    x-on:click="$dispatch('open-modal', { id: 'modal-filtri' })"
    label="{{ __('fixcity::segnalazione.filters.open') }}"
/>

{{-- Modale --}}
<x-filament::modal id="modal-filtri" slide-over>
    <x-slot name="heading">{{ __('fixcity::segnalazione.filters.title') }}</x-slot>
    @include('fixcity::components.filters.sidebar')
</x-filament::modal>
```

---

## Alpine.store Pattern per Tabs Cross-Scope

Quando `x-filament::tabs` e i pannelli sono in scope Alpine diversi:

```javascript
// Themes/Sixteen/resources/js/app.js
AlpineInstance.store('segnalazioniTabs', {
    active: 'map',
    setTab(tab) {
        this.active = tab;
        if (tab === 'map') {
            setTimeout(() => {
                const mapEl = document.getElementById('ticket-map');
                if (mapEl?.invalidateSize) mapEl.invalidateSize();
            }, 50);
        }
    },
});
```

---

## Dispatch Modal Pattern

```blade
{{-- Aprire modale da Alpine --}}
x-on:click="$dispatch('open-modal', { id: 'modal-id' })"

{{-- Chiudere modale da Alpine --}}
x-on:click="$dispatch('close-modal', { id: 'modal-id' })"

{{-- Aprire modale da Livewire PHP --}}
$this->dispatch('open-modal', id: 'modal-id');
```

---

## Riferimenti

- Doc completo: `laravel/Themes/Sixteen/docs/wiki/concepts/filament-5x-blade-components-reference.md`
- STORY-068: `docs/stories/STORY-068-it-tabs-filament-5x-correct-pattern.md`
- Issue #153: https://github.com/laraxot/base_fixcity_fila5/issues/153
- Discussion #154: https://github.com/laraxot/base_fixcity_fila5/discussions/154
