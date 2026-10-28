---
title: "Filament Blueprint — Fixcity"
type: filament-blueprint
status: active
module: Fixcity
created: 2026-10-16
updated: 2026-10-22
tags: [bmad, filament, blueprint, fixcity]
qmd: "filament blueprint fixcity xotbase resource widget"
related:
  - ../bmad-planning.md
  - ../bmad-brainstorming.md
---

# Scopo

Contratto Filament del modulo **Fixcity**: chrome BO e/o widget FO senza violare
i confini di dominio.

## Regole non negoziabili

0. PHPStan Modules a zero: errori in `app/Filament/*` → **scopo** (questo blueprint) → chiamanti → tipi veri; **mai zittire** (ignore/baseline/exclude). Gate: `cd laravel && ./vendor/bin/phpstan analyse Modules` ([phpstan-modules-zero-standing-order](../../../Xot/docs/wiki/memories/phpstan-modules-zero-standing-order.md), [00-start.md](../../../../../bashscripts/docs/prompts/00-start.md#ordine-permanente-dellutente), [11-phpstan.md](../../../../../bashscripts/docs/prompts/11-phpstan.md)).
1. Estendere solo `XotBaseResource` / `XotBaseWidget` / basi Xot — mai classi Filament dirette.
2. Nessun `->label()` hardcoded: chiavi in `lang/<locale>/` del modulo.
3. Business logic nelle Actions del modulo, non nei form hooks grassi.
4. FO: widget montati esplicitamente; non abusare `discoverWidgets` per chrome.

## Resource / widget

- `TicketResource`

Le FAQ pubbliche non sono un blocco del tema. Sono contenuto di prodotto:
`Faq` sulla connessione `fixcity`, catalogo `CivicFaqCatalog`, `FaqSeeder`
(idempotente, anche fuori dalla demo) e `FaqFactory` per i test. La pagina
`/it/domande-frequenti` le legge con `ListPublishedFaqsAction`. Una Resource
BO, quando servirà all'ufficio, estende `XotBaseResource` e non mette label
nel form.

Le migration `create_{modello}_table` non dichiarano `$model_class`.
La Resource usa lo stesso modello che `XotBaseMigration` ricava dal nome file.

## Form / Table / Infolist

Preferire triad schema dedicati (`*Form`, `*Table`, `*Infolist`) quando la Resource
cresce oltre pochi campi. Enum tipizzati al posto di option array.

## Policy e tenant

Ogni query Filament rispetta tenant + policy del modello owner. Nessun elenco
cross-tenant per comodita admin.

## Related

- Planning: [bmad-planning.md](../bmad-planning.md)
- Brainstorming: [bmad-brainstorming.md](../bmad-brainstorming.md)


## Lifecycle segnalazione (SSoT dettagliato)

- [ticket-lifecycle-filament-blueprint.md](./ticket-lifecycle-filament-blueprint.md)
- [ticket-lifecycle-blueprint.md](./ticket-lifecycle-blueprint.md)
- Ledger: `TicketActivity` — **non** `timeline_steps`.

## Upload da fotocamera mobile

Il campo `TicketForm::images` mantiene la whitelist server `image/jpeg`, `image/png`,
`application/pdf`. Il picker del tema puo' esporre anche `image/heic` e `image/heif`
per i dispositivi iOS, ma `dc-file-upload.blade.php` li converte in JPEG prima di
`$wire.upload()`. Non ampliare la collezione Media Library per risolvere il problema:
la conversione client mantiene una sola regola di sicurezza e impedisce il messaggio
generico di upload fallito per le foto scattate dalla fotocamera.

Il testo mostrato dal picker non vive nel Blade o nel JavaScript: arriva dalle chiavi
`fixcity::ticket_form.fields.images.*`, presenti in `it`, `en`, `de` ed `es`. La soglia
applicativa resta 10 MB per file; il web-root `public_html/.user.ini` imposta 12 MB per
file e 16 MB per la richiesta multipart. Nginx deve avere `client_max_body_size 16M`
(o superiore) e il runtime PHP deve consentire gli stessi valori, altrimenti PHP
risponde `validation.uploaded` prima che Laravel possa validare il campo.

## Etichette ciclo su ViewTicket (qualita)

Le azioni header (`approve`, `reject`, `resolve`, `close`, `changeStatus`,
`send_communication`, …) leggono `fixcity::ticket.actions.*` — le chiavi devono
essere copy reale per l'operatore, non stub. Canon: [filament-viewticket-lifecycle-labels-quality.story.md](../stories/filament-viewticket-lifecycle-labels-quality.story.md).

## FO dettaglio (Sixteen + lang)

Il blocco test `segnalazione-dettaglio` e il dettaglio pubblico usano
`fixcity::ticket_detail.*` (breadcrumb, meta, where, description). Nessun
`->label()` / stringa UI nel blade. Widget rating cittadino: solo `@auth` +
`TicketCitizenRatingPromptWidget` (XotBase), montaggio esplicito.
Gli ID del titolo della regione rating derivano dal ticket ID e `aria-labelledby`
deve puntare a quello stesso ID, anche quando la view e' riusata.

## FO link / navigazione (non Filament)

Chrome FO = JSON header/footer + Folio/CMS, non Resource Filament.
**Religione URL:** solo `route('container0.index|view', …)` (e `login` /
`logout` POST). Vietati `LaravelLocalization::localizeUrl` nel Blade e
`route('tickets.show|create|track')`. Canon:
[frontoffice-link-conventions.md](../frontoffice-link-conventions.md).
Audit: [fo-link-coherence-audit.story.md](../stories/fo-link-coherence-audit.story.md).

**Religione i18n:** sito `it|en|de|es`; nessun testo UI hardcoded nel Blade.
Chrome tema → `pub_theme::` (es. `navigation.homepage.news`); dominio
Fixcity → `fixcity::…` in `lang/<locale>/`; Filament senza `->label()`.
Governance: [translation-key-governance.md](../../../Lang/docs/wiki/rules/translation-key-governance.md).

## CreateTicketWidget / location

Il passo dati riusa `TicketForm::getDataSchema()`: il punto mappa e'
`data.location.{latitude,longitude,address}` (CoordinatePicker + `coordinatesRule`).
I test non devono impostare `latitude`/`longitude` piatti. Ore DMO:
`ticket_hours.user_id` e' UUID (`varchar(36)`), vedi
[ticket-hours-user-boundary](../decisions/ticket-hours-user-boundary.md).
