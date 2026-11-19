---
title: "Sitemap + Folio routing per ticket (`/it/tickets/{id}`)"
type: concept
status: active
tags: [fixcity, sitemap, folio, dcp-273, dcp-148]
created: 2026-10-26
updated: 2026-10-26
qmd: "perché `/it/tickets/35` può mancare dalla sitemap (analisi profonda) fixcity"
---

# Perché `/it/tickets/35` può mancare dalla sitemap (analisi profonda)

## Il percorso reale (non nel modulo Fixcity)

Il dettaglio di un ticket **non ha una blade nel modulo**. La vista è nel tema:
`Themes/Sixteen/resources/views/pages/[container0]/[slug0]/index.blade.php` (Volt, `container0.view`).
Quando si accede a `/it/tickets/35`:
- Folio estrae `container0=tickets`, `slug0=35`
- `PageSlugMiddleware` risolve la pagina CMS
- `ResolvePageAction::execute('tickets','35')` priortizza: 1) modello dinamico (`Ticket::class` tramite `container0_model_map`), 2) slug esatto `tickets.35`, 3) generico `tickets.view`
- Se il ticket è pubblico (`status` in `TicketStatusEnum::canViewByAll()` e `location` non null), `BuildPublicTicketDetailAction` lo carica
- Il componente Volt `new class extends Component` renderizza il dettaglio

## La sitemap usa lo STESSO query

`ListPublicTicketPathsAction::execute()` chiama `BuildPublicTicketsQueryAction::execute(includeOwnTickets: false)` — la stessa query della pagina pubblica. Quindi un URL è in sitemap **se e solo se** risponde con quel contenuto (DCP-273).

## Perché può risultare mancante

Se `/it/tickets/35` non appare in `sitemap.xml`, le cause sono **dati o runtime**, non codice:
1. `Ticket::find(35)` esiste ma `status` non è in `canViewByAll()` → query sitemap lo esclude
2. `location` è `NULL` → `whereNotNull('location')` lo esclude
3. `includeOwnTickets: false` ma il proprietario loggato non ha il ticket pubblico → stessa esclusione per tutti
4. Module `Fixcity` non caricato (provider non registrato) → tag `SitemapPathsContract` vuoto

## Checklist verifica

- `php artisan tinker`: `Ticket::find(35)->status`, `location`
- `grep -n 'tags' laravel/config/app.php` o `module.json`: `FixcityServiceProvider` attivo
- `curl /sitemap.xml | grep 'tickets/35'`
- `Modules/Seo/app/Actions/Sitemap/BuildSitemapAction.php:87-91`: verifica che `app()->tagged(SitemapPathsContract::TAG)` includa `ListPublicTicketPathsAction`
