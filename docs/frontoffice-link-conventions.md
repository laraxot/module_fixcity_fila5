---
title: "Convenzioni link front office"
type: concept
status: active
module: Fixcity
updated: 2026-10-22
tags: [bmad, folio, routing, religion, front-office]
qmd: "frontoffice link conventions container0.view localizeUrl tickets.show vietato"
related:
  - bmad/filament-blueprint.md
  - decision-log.md
  - stories/fo-link-coherence-audit.story.md
  - links.md
  - ../../../Themes/Sixteen/docs/wiki/concepts/fo-folio-named-routes-header.md
---

<!--
Supersedes: bozza precedente nello stesso path (mancava religione anti-localizeUrl).
Linked from: links.md, filament-blueprint, decision-log, fo-link-coherence-audit.
User: «fare LaravelLocalization::localizeUrl è sbagliato… corretta era route(container0.view)…
non rifarlo più» + BMAD/second brain/filament blueprint + docs moduli/temi.
-->

# Convenzioni link front office — RELIGIONE

## Perché (senso)

Il FO non è un albero di rotte Laravel classiche. È una **shell Folio del tema**
`[container0]/[slug0]` con `name('container0.view')` / `name('container0.index')`
che risolve JSON CMS + blocchi. Le pagine Folio del modulo con nomi tipo
`tickets.create` / `tickets.show` restano in `folio:list` ma **non servono** la
risposta HTTP: il mount del tema le ombreggia.

Quindi:

1. **`route('tickets.show', …)` / `tickets.create` / `tickets.track`** — sbagliato
   come destinazione FO. Punta a un nome che non è la shell che risponde.
2. **`LaravelLocalization::localizeUrl('/…')` nel Blade** — sbagliato per pagine
   Folio. Due fonti di verità (path string vs Folio), regressioni, e agenti che
   reincarnano lo stesso errore. La localizzazione del prefisso lingua è
   responsabilità della **rotta Folio**, non del Blade.

## Canon (copia/incolla)

```blade
{{ route('container0.index', ['container0' => 'tickets']) }}
{{ route('container0.view', ['container0' => 'tickets', 'slug0' => $ticket->getKey()]) }}
{{ route('container0.view', ['container0' => 'tickets', 'slug0' => 'create']) }}
{{ route('container0.view', ['container0' => 'tickets', 'slug0' => 'track']) }}
{{ route('container0.view', ['container0' => 'area-personale', 'slug0' => 'impostazioni']) }}
{{ route('login') }}
```

Track con codice:

```php
route('container0.view', ['container0' => 'tickets', 'slug0' => 'track'])
    .'?code='.rawurlencode($code);
```

## Eccezioni ammesse

| Caso | Strumento |
| --- | --- |
| Path da JSON CMS senza nome Folio | `FrontofficeUrl::fromStoredUrl($url)` o path `/tickets/...` (normalizzato da `ResolveLocalizedBlockDataAction`) |
| Espressione nei blocchi CMS (`HasBlocks`) | solo `{{ route('container0…') }}` — **non** `localizeURL` |
| Path generico utility | `FrontofficeUrl::path($path)` — mai facciata Mcamara nel Blade |

## Vietato

```blade
{{ LaravelLocalization::localizeUrl('/tickets/create') }}
{{ route('tickets.show', $ticket) }}
{{ route('tickets.create') }}
href="/{{ app()->getLocale() }}/tickets/…"
```

## Test anti-regressione

- `tests/Unit/Views/FrontofficeTicketLinksContractTest.php`
- `tests/Unit/Views/FrontofficeNamedRoutesContractTest.php`

## Nota locale

Folio può far vincere l’ultima lingua sul `name()` duplicato (DCP-123): si
corregge nel provider, **non** con `localizeUrl` nei template.

## Copy / i18n (stesso contratto BMAD)

Il sito è **multilingua** (`it|en|de|es`). Gli URL usano `route('container0…')`;
le **label** usano chiavi, mai frasi nel Blade.

| Layer | Namespace | Esempio |
| --- | --- | --- |
| Chrome tema (nav, breadcrumb, header) | `pub_theme::` | `pub_theme::navigation.homepage.news` |
| Dominio prodotto (ticket, FAQ, form) | `fixcity::` / modulo owner | `fixcity::ticket_detail.*` |
| Filament BO | modulo owner, no `->label()` | `fixcity::ticket.actions.*` |

Pattern chiave: `module::context.collection.element.type` (governance Lang).
Canon: [translation-key-governance.md](../../Lang/docs/wiki/rules/translation-key-governance.md) ·
[fo-header-url-and-translation-contract.md](../../../Themes/Sixteen/docs/wiki/concepts/fo-header-url-and-translation-contract.md).

## Related

- [decision-log.md](./decision-log.md) · [filament-blueprint.md](./bmad/filament-blueprint.md)
- [fo-folio-named-routes-header.md](../../../Themes/Sixteen/docs/wiki/concepts/fo-folio-named-routes-header.md)
