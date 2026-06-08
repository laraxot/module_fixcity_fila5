# Fixcity — `ticket` vs `segnalazione`: quale lingua per lo schema Filament

## Regola operativa

| Contesto codice | Namespace traduzioni | File tipico |
|-----------------|---------------------|-------------|
| Schema Filament su dominio **`Ticket`** (`TicketForm`, heading `Section`, pulsanti/action legati alla **resource** Ticket) | **`fixcity::ticket.*`** | `Modules/Fixcity/lang/*/ticket.php` |
| Pagine pubbliche, parity Design Comuni, copy blocchi CMS wizard “cittadino”, non legato al bounded context Laravel `TicketResource` | **`fixcity::segnalazione.*`** | `Modules/Fixcity/lang/*/segnalazione.php` |

Esempi corretti sugli heading del wizard canale ticket:

```php
// TicketForm — stesso bounded context della Resource Filament Ticket
__('fixcity::ticket.sections.summary.label');
__('fixcity::ticket.sections.author.label');
__('fixcity::ticket.sections.contacts.label');
```

**Non usare** per quegli heading:

```php
__('fixcity::segnalazione.sections.summary.label'); // SBAGLIATO qui: altro dominio i18n
```

## Perché (philosophia / religione progettuale)

1. **Allineamento al modello Laravel** — In codice PHP il concetto persistente è `Ticket`; `ticket.php` raggruppa le stringhe degli stessi file che Filament e il LangServiceProvider risolvono per la **resource** (campi, sezioni, navigazione). `segnalazione` descrive il **prodotto comunicato** al cittadino, non il contratto tecnico del modello.
2. **Una sola SSoT per la maschera Ticket** — Backoffice create/edit e wizard che riusano `TicketForm` condividono `ticket.sections.*` senza duplicare voci quasi uguali fra `ticket` e `segnalazione`.
3. **Drift consapevole** — Se serve copy diversa sulla landing pubblica (`segnalazione`) dalla label Filament (`ticket`), è una decisione esplicita; non si “prende la chiave pubblica” per il layer schema senza rendersene conto.
4. **Coerenza con la religione i18n del monorepo** — Vedi anche [translation-namespace-religion.md](../../../../../../docs/translation-namespace-religion.md): namespace per **dominio business** (`ticket`), non necessariamente per il nome slug della pagina marketing.

## Dove si applica in codice

- **Schema Filament** (`TicketForm`, `TicketFormReviewInfolist`) e vista legacy riepilogo modulo: [`summary.blade.php`](../../../resources/views/filament/widgets/wizard/steps/summary.blade.php) → solo **`fixcity::ticket.*`**.

## Riferimenti interni

- [filament-summary-infolist-guidance.md](../../filament-summary-infolist-guidance.md) — namespace `ticket` per heading `Section` del riepilogo wizard
- [filament5-schema-namespaces-and-wizard-summary.md](./filament5-schema-namespaces-and-wizard-summary.md) — `TextEntry` + `Get`
- Regola root: [filament-wizard-summary-infolist-rule.md](../../../../../../docs/wiki/concepts/filament-wizard-summary-infolist-rule.md)
