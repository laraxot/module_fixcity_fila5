---
id: swarm-phpstan-ticketform-scope-fixcity
title: "Swarm PHPStan: TicketForm di Fixcity, famiglie da ground truth e tipizzazione alla sorgente"
type: story
status: verify-pending
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-15
updated: 2026-10-15
qmd: "swarm phpstan ticketform fixcity filament v5 step select enum filament auth reviewFormState"
related: [dcp-179, dcp-33, dcp-128]
---

# Swarm PHPStan: TicketForm di Fixcity, famiglie da ground truth e tipizzazione alla sorgente

Fase BMAD: Quick Flow (fix mirato su un solo file). Perimetro assoluto: `laravel/Modules/Fixcity/`.
Secondo cervello consultato via QMD/wiki non disponibile in questa sessione (nessuna shell):
si e' lavorato sulle fonti primarie (codice, vendor, test, report esistenti, story DCP-179).

## Richiesta

Brief swarm: correggere `TicketForm.php` (Wizard, EnumSelect, Auth, casi enum, `@return`,
`?->`/`??`) e ogni altro file Fixcity presente in `phpstan_output.txt`,
`phpstan_modules_initial.json`, `themes_phpstan.json`. Vincoli: mai `phpstan.neon`,
mai baseline/ignore/`@phpstan-ignore`/`@var` per silenziare, forward-only, logica nuova
solo in Spatie QueueableAction, test solo in `Modules/Fixcity/tests/`, scope disgiunto.

## Scoperta principale: la ground truth e' stale, il file e' gia' migrato

`TicketForm.php` su disco (mtime 2026-10-15 08:23) **non contiene piu' nessuna** delle righe
segnalate in `phpstan_output.txt` (run di maggio: righe 40-44, 116-121, 198-200, 251).
La riscrittura e' successiva all'ultimo verde misurato (DCP-179: `[OK] No errors` il
2026-10-14 20:33). Scopo ricostruito del file attuale, confermato dai test
(`tests/Unit/TicketFormWizardSummarySchemaTest.php`,
`tests/Feature/Livewire/TicketFormRenderTest.php`,
`tests/Feature/Livewire/TicketFormValidationTest.php`,
`tests/Unit/Enums/TicketTypeEnumTest.php`):

- wizard front office `/tickets/create` (privacy -> dati -> riepilogo) + schema piatto back office;
- passo dati in un solo `View` Design Comuni, stato e regole invariati;
- riepilogo alimentato da `BuildTicketReviewSummaryAction` (QueueableAction, non Service);
- autore/contatti solo dall'utente `Filament::auth()`, mai inventati (niente codice fiscale).

Altri file Fixcity nei report: **nessuno**. `phpstan_modules_initial.json` cita solo
Lang/Media/Tenant (parse) e internal error altrove; `themes_phpstan.json` solo Sixteen;
`phpstan_output.txt` (169 errori, ordinato per path) ha un unico blocco Fixcity: TicketForm.

## Famiglie (una radice ciascuna) ed esito

| # | Identifier | Causa radice | Stato |
|---|---|---|---|
| 1 | `class.notFound` `Filament\Forms\Components\Wizard\Wizard` + `method.nonObject` a cascata | namespace Filament v4, in v5 e' `Filament\Schemas\Components\Wizard` | gia' risolto: il file usa `Step` (vendor: esiste, `extends Component`, `make(string): static`) |
| 2 | `return.type` `getFormSchema()` `array<int, mixed>` | schema da elementi misti | gia' risolto: ogni metodo dichiara `@return array<string, SchemaComponent>`; il parent `XotBaseResourceForm::getFormSchema()` e' abstract con `@return array<string, Component>` (stesso alias). `getSteps()` torna `array<string, Step>`, covariante. Nota: il brief chiedeva `array<int, Component>`, ma il parent reale vuole `array<string, Component>`: si segue il parent |
| 3 | `class.notFound` `EnumSelect` + `method.nonObject` a cascata, `argument.type` su `schema()` | `EnumSelect` non esiste in Filament v5 | gia' risolto: `Select::options(TicketTypeEnum::class)`; firma vendor `options(array\|Arrayable\|string\|Closure\|null)` con `enum_exists()` -> `enum()` verificata; `schema()` in v5 accetta `array<Component\|Action\|ActionGroup\|string\|Htmlable>` (trait `HasChildComponents`), quindi il vecchio `argument.type` non si applica piu' |
| 4 | `classConstant.notFound` `TicketTypeEnum::Segnalazione/Richiesta/Feedback` | casi mai esistiti con quei nomi | gia' risolto: l'enum reale ha casi UPPER_SNAKE (`ROAD_MAINTENANCE` ... `COMPLAINT/SUGGESTION/REPORT/REQUEST/OTHER`, `default()` = `OTHER`, `getLabel()` da `EnumTrait`, fissati da `TicketTypeEnumTest`); il form non referenzia piu' casi rimossi |
| 5 | `class.notFound` `Auth` + `property.nonObject` + `nullsafe.neverNull` | `Auth` non importato, risolto al namespace relativo | gia' risolto: `Filament::auth()` (`FilamentManager::auth(): Guard` verificato; `user(): ?Authenticatable`, `check(): bool`) + `data_get()` + `SafeStringCastAction::cast(mixed): string`. I `??`/`?->` rimasti sono tutti su `mixed`/array (`$point[...] ?? ...`, `$state[...] ?? null`, `$enum?->getLabel() ?? $raw` con `$enum` `?TicketTypeEnum`: nullsafe **necessario**); nel file non c'e' alcuna `BelongsTo`, quindi la verifica `nullable()` delle FK e' N/A |

Dettagli firme verificate in vendor (senza shell, per lettura diretta):

- `filament/schemas`: `Wizard/Step.php`, `View.php` (`make(string)`, `viewData(array|Closure)` con `array<string, mixed>`), `Section.php`, `Html.php` (`make(string|Htmlable|Closure|null)`), `Component.php` + concerns (`visible(bool|Closure)`, `hiddenLabel()`, `id(string|Closure|null)`), `HasChildComponents::schema()`.
- `filament/forms`: `Field extends Schema Component`; `Select` + `HasOptions`; `Checkbox` + `HasExtraInputAttributes::extraInputAttributes(array|Closure)`; `CanBeValidated` (`rule/required/validationMessages`); `CanBeNative`, `CanBeSearchable`; concerns `HasHelperText`, `HasExtraFieldWrapperAttributes`.
- `filament/infolists`: `TextEntry extends Entry extends Schema Component` (quindi `summarySectionEntries(): array<string, SchemaComponent>` e' esatto).
- `filament/filament`: `FilamentManager::auth(): Guard`. `livewire`: `HandlesValidation::getErrorBag()` esiste e `setErrorBag()` wrappa sempre in `MessageBag`, quindi la guardia `instanceof MessageBag` nel form e' vera a runtime (nessun bug `aria-invalid`).
- `Geo`: `CoordinatePicker` + `HasCoordinatePicker::geolocateWhenEmpty()/reverseGeocoding()`; `XotBaseField extends Field`.
- `Xot`: `EnumTrait::getLabel(): string`; `SafeStringCastAction::cast(mixed): string`.
- `thecodingmachine/safe`: `Safe\preg_replace` disponibile (`use function` valido).
- Viste `fixcity::*` referenziate dal form: tutte esistenti in `Modules/Fixcity/resources/views`. `->view()` accetta `?string` semplice (non `view-string`): nessun controllo di esistenza da PHPStan comunque.
- `GetPublishedPrivacyPolicyAction::execute(): ?string`; `BuildTicketReviewSummaryAction::execute(array<string, mixed>, ?Authenticatable): array{...}` (shape documentata).

## Unica modifica di questa sessione

`reviewFormState()`: tipizzazione alla sorgente del confine `data_get() -> mixed`.

```diff
- array_filter((array) data_get($livewire, 'data', []), is_string(...), ARRAY_FILTER_USE_KEY),
+ static::reviewFormState($livewire),
```

Helper `private static reviewFormState(object $livewire): array<string, mixed>` con ciclo
esplicito sulle sole chiavi stringa. Semantica identica al filtro precedente (chiavi non
stringa scartate, anche il caso non-array che prima dava `[]` via cast+filtro). Nessuna
logica di dominio nuova, nessun contratto toccato (metodo nuovo, privato).

## Verifica

- `php -l`, `phpstan analyse Modules/Fixcity/...`, Pest mirato: **BLOCCATI** (nessuna shell
  nell'ambiente: `DAYTONA_API_KEY` assente; `git log -p`, `rg` sui chiamanti, `lock/*.sh`,
  `md-filenames.py`, `llm-wiki-qmd.sh` tutti non eseguibili). **Verde non dichiarato.**
- Evidenza alternativa prodotta: rilettura post-edit del file + tabella firme qui sopra.
- Handoff al coordinatore: rieseguire `php -l`, `phpstan analyse
  Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`,
  Pest (`TicketFormWizardSummarySchemaTest`, `TicketFormValidationTest`,
  `TicketFormRenderTest`, `TicketTypeEnumTest`) e il run canonico completo.

## Decisioni

- D10: nessun tocco a `phpstan.neon`, baseline, ignore, `@phpstan-ignore`, `@var`/`cast` cosmetici.
- D11: `@return` segue il parent reale (`array<string, Component>`), non il suggerimento stale del brief.
- D12: nessun file fuori `Modules/Fixcity/`; nessuna logica in `*Service` (gia' in QueueableAction).
- D13 (deviazione dichiarata): lock via `bashscripts/lock/*.sh` impossibile senza shell;
  mitigazione: scope disgiunto assegnato, un solo file modificato, nessun claim concorrente noto.

## Aperto

- [ ] Gate di verifica (coordinatore/sessione con shell): lint + phpstan scoped + Pest + run canonico.
- [ ] `TicketFormReviewInfolist.php:39`: `/** @var mixed $location */` ridondante (`Get::__invoke` torna gia' `mixed`); innocuo, candidato a rimozione in un passaggio con shell.
- [ ] Reorg `Modules/Fixcity/docs/` (fuori portata senza shell: rinominare richiede `md-filenames.py` + aggiornamento link + `qmd update` del coordinatore): duplicati rilevati `phpstan-fix-plan.md`, `phpstan-fixes.md`, `phpstan-immediate-fixes.md`, `phpstan-level-10-fixes.md`, `phpstan-standards.md` + `phpstan/*.json`; `redundancy-audit.md`, `redundancy-audit-fixcity.md`, `redundancy-report.md`, `CENTRAL_REDUNDANCY_REPORT.md`; indici multipli (`00-INDEX.md`, `INDEX.md`, `index.md`, `DOCUMENTATION_INDEX.md`, `DOCUMENTATION_AUDIT.md`); molti file MAIUSCOLI contro la convenzione kebab-case. Questa story linka invece di copiare: [ticketform-schema-guidelines](../ticketform-schema-guidelines.md), [filament-select-enum-best-practices](../filament-select-enum-best-practices.md), [filament-wizard-pattern](../filament-wizard-pattern.md), [DCP-179](./dcp-179-phpstan-zero-su-modules-e-default-di-config-env.story.md).

## Errori iniziali/finali

- Iniziali (da report, versione maggio del file, 19 voci su TicketForm): vedi tabella famiglie.
- Finali su disco: 0 voci residue riconducibili alle famiglie (verifica per firme); conteggio strumentale pendente (vedi Handoff).
