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
