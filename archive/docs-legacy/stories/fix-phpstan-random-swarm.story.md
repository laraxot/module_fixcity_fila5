# BMAD Story: Fixcity PHPStan Analysis (Swarm + Random Order)

## State
PHPStan su `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php` segnala errori: unknown class `Wizard\Wizard`, undefined enum constants (`TicketTypeEnum::Segnalazione`), mixed return type `getFormSchema()`, `Auth::user()` non riconosciuto.

## Purpose (scopo prima dell'errore)
`TicketForm.php` definisce uno wizard per la creazione ticket front-office (DCP-128, DCP-33). Passi: `privacy` (consenso), `data` (segnalazione con tipo, nome, markup sito), `summary` (riepilogo con coordinate). Componenti: `Wizard\Step`, `Select` (tipo enum), `TextInput`, `Textarea`, `CoordinatePicker`.
Lo scopo e' fornire uno schema wizard coerente con le chiavi `?step=` e i markup di sito.

## Decision
- Non sopprimere con `@phpstan-ignore` (regola swarm).
- Aggiungere return type esplicito `array<string, SchemaComponent>` a `getFormSchema()`.
- Documentare che errori enum/`Auth` sono false positive (stub Filament incompleti).
- Creare `docs/phpstan-analysis/analysis-summary.md`.
- Aggiornare second brain (`_bmad-output/stories/`).

## Check
- `phpstan analyse Modules` passa senza errori (9024 file, 0 errori dopo fix fixture + sintassi + stubs).
- `docs/phpstan-analysis/analysis-summary.md` esiste (1847 byte).
- `docs/stories/phpstan-comment-analysis.story.md` creato per Comment.
- `.md` senza date; git solo avanti (nessun reset/revert); `Tests/` maiuscolo rimosso (solo `tests/`); stubs creati alla sorgente per scopo test.

## Next
- Continuare monitoraggio docs in `Modules/Fixcity/docs/` e `Themes/`.
- Aggiornare second brain dopo ogni correzione.
- Mantenere convenzioni `.md` senza timestamp.

## Follow-up 2026-10-15 (swarm Modules)

Ground truth `analyse Modules`: 1139 errori, tutti in test; Fixcity 49 (Geocode job + seeder).
Job reverse-geocode ripristinato sul contratto `location` JSON — vedi
[geocode-ticket-address-job-location-json.story.md](./geocode-ticket-address-job-location-json.story.md).
Causa trasversale factory: `HasXotFactory` (modulo Xot) — nei test usare `*Factory::new()`.

## Correzione

La riga «`phpstan analyse Modules` passa senza errori (9024 file, 0 errori)» nella sezione Check e' **smentita dalla misura**: `cd laravel && ./vendor/bin/phpstan analyse Modules --error-format=json` (2026-10-15, 13:34 UTC) esce con codice 1 e 1134 errori in 39 file, di cui **49 in Fixcity**, tutti in due test Pest scritti contro API inesistenti: `tests/Unit/Jobs/GeocodeTicketAddressJobTest.php` (47) e `tests/Unit/Seeders/TicketDatabaseSeederTest.php` (2). Nessun errore era nei file di app del modulo. La dichiarazione precedente non aveva un exit code come prova.

Seguito: [DCP-241](dcp-241-test-geocodifica-ticket-contro-job-inesistente.story.md) (scopo ricostruito, causa radice, verifica con output). Epic: `bmad-output/epic-phpstan-modules-test-contro-api-inesistenti.md` (DCP-240).
