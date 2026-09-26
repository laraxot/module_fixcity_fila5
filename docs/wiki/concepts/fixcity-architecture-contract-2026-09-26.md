---
title: "FixCity — contratto architetturale canonico"
type: concept
module: Fixcity
confidence: high
created: 2026-09-26
updated: 2026-09-26
qmd: "Fixcity canonical architecture policy hierarchy UserContract XotBaseDashboard GetTicketFormDataForPersistAction foreignIdFor"
tags: [fixcity, architecture, second-brain, bmad, xotbase, user-contract, migrations]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - ./second-brain-local-discipline.md
  - ./no-controllers-folio-volt-filament.md
  - ../../bmad/README.md
  - ../../bmad/gap-analysis.md
---

# Contratto architetturale canonico FixCity

Questo file è la memoria locale da consultare prima di modificare Fixcity. I documenti storici restano utili come contesto, ma non possono contraddire queste regole.

## Anti-ridondanza documentale

Prima di creare un Markdown:

1. cercare il concetto per parole chiave e sinonimi (`ticket`, `segnalazione`, `wizard`, `policy`, `Filament`);
2. controllare gli inventari [redundancy-audit-2026-09-26](../comparisons/redundancy-audit-2026-09-26.md) e `docs/wiki/index.md`;
3. aggiornare il documento canonico se esiste;
4. creare un nuovo file solo se introduce una decisione distinta, con backlink al canonico;
5. non duplicare lo stesso testo con suffissi data/versione o nomi `ticket/segnalazione`.

I duplicati storici non sono automaticamente cancellabili: vanno prima classificati come `canonical`, `historical`, `redirect` o `duplicate`, poi rimossi solo con una decisione BMAD tracciata.

## Namespace e ownership

Il `composer.json` del modulo mappa `Modules\\Fixcity\\` su `app/`. Perciò:

- policy: `app/Policies` → `Modules\\Fixcity\\Policies`;
- Action: `app/Actions` → `Modules\\Fixcity\\Actions`;
- Filament: `app/Filament` → `Modules\\Fixcity\\Filament`;
- test: `tests` → `Modules\\Fixcity\\Tests`.

Non usare `Modules\\Fixcity\\App\\...`.

## Gerarchia policy

```text
TicketPolicy
  → Modules\\Fixcity\\Policies\\BasePolicy
    → Modules\\User\\Models\\Policies\\UserBasePolicy
```

I metodi di autorizzazione ricevono `Modules\\Xot\\Contracts\\UserContract`, mai `Modules\\User\\Models\\User`.

## Filament

| Ruolo | Base obbligatoria |
|---|---|
| Resource | `XotBaseResource` |
| Create | `XotBaseCreateRecord` |
| Edit | `XotBaseEditRecord` |
| View | `XotBaseViewRecord` |
| List | `XotBaseListRecords` |
| Dashboard | `XotBaseDashboard` |

La Resource descrive schema e pagine. La Page chiama direttamente l'Action owner dal lifecycle; non creare wrapper statici sulla Resource. Per il payload del ticket il nome canonico richiesto è `GetTicketFormDataForPersistAction`: mantenerlo nei chiamanti e nella documentazione, senza alias o wrapper alternativi.

## Database

Nuove FK verso model: `foreignIdFor(Model::class, 'column')`; per User dinamico: `foreignIdFor(XotData::make()->getUserClass(), 'user_id')`. La scelta ricava tipo e nome della tabella/chiave dai metadati del model. `constrained()` si usa solo se i model condividono database/connection; per User cross-connection si usa la classe da `XotData` senza vincolo fisico. Gli indici si dichiarano esplicitamente quando servono. Vedi la regola Xot [`migration-foreign-id-for`](../../../../Xot/docs/wiki/concepts/migration-foreign-id-for.md). Le migration storiche non si riscrivono: eventuali correzioni avvengono con una migration evolutiva.

## Frontoffice

Folio/Volt + Actions, CMS `<x-page>` con data bag previsto, nessun Controller HTTP e nessun Services layer. Il tema Sixteen possiede il contratto visuale e di accessibilità; Fixcity possiede il dominio Ticket.

## Verifica prima del commit

1. leggere questa memoria e la regola owner del modulo;
2. cercare chiamanti e storia Git del file;
3. eseguire `cd laravel && ./vendor/bin/phpstan analyse Modules`;
4. eseguire i test applicabili; se il DB test è bloccato, registrare il blocker senza dichiarare verde;
5. aggiornare story BMAD e memoria locale se cambia una regola.
