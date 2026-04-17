---
type: overview
module: Fixcity
sources:
  - ../../../README.md
  - ../../../ticket-wizard-frontoffice.md
  - ../../../structure.md
  - ../../../wizard-governance-philosophy.md
  - ../../../filament-wizard-pattern.md
confidence: high
updated: 2026-04-15
---

# Fixcity Module — Overview

> **Ruolo**: Sistema di ticketing civico — gestione segnalazioni, wizard frontoffice per cittadini, pannello operatori Filament.

## Responsabilità del Modulo

Il modulo Fixcity è il **core business** dell'applicazione FixCity:

- Gestisce il ciclo di vita dei `Ticket` (segnalazioni civiche)
- Fornisce un **Wizard frontoffice** guidato per i cittadini (`CreateTicketWizardWidget`)
- Espone il pannello operatori Filament per gestione interna
- Si integra con `Geo` (geolocalizzazione), `Sixteen` (CSS Design Comuni), `Xot` (base classes)
- Multi-lingua completo (`fixcity::` namespace, tutte le lingue)

## Modelli Core

| Modello | Scopo | Note |
|---------|-------|------|
| `Ticket` | Segnalazione civica principale | owner_id = User; priority/status/type enum |
| `TicketPriorityEnum` | Priorità (LOW, MEDIUM, HIGH, CRITICAL) | PHP enum |
| `TicketStatusEnum` | Stato (OPEN, IN_PROGRESS, CLOSED, ecc.) | PHP enum |
| `TicketTypeEnum` | Tipo (TECHNICAL, REPORT, ecc.) | PHP enum |

```php
$ticket = Ticket::query()->create([
    'name'     => 'Buche in strada',
    'content'  => 'Descrizione dettagliata',
    'priority' => TicketPriorityEnum::HIGH,
    'status'   => TicketStatusEnum::OPEN,
    'type'     => TicketTypeEnum::TECHNICAL,
    'owner_id' => auth()->id(),
]);
```

## Wizard Frontoffice Cittadino

### Architettura

Il wizard è il flusso principale per la creazione di segnalazioni da parte dei cittadini:

- **URL**: `/it/tests/segnalazione-crea`
- **Widget**: `CreateTicketWizardWidget` extends `XotBaseWizardWidget`
- **Composizione pagina**: CMS-driven via `content_blocks` JSON (breadcrumb + segnalazione-crea + contacts-card)
- **Filament Wizard**: `Filament\Schemas\Components\Wizard` con `Step` components

### Step del Wizard

| Step | Nome | Contenuto |
|------|------|-----------|
| 1 | Privacy | Copy GDPR first-class + checkbox accettazione (MAI solo checkbox) |
<<<<<<< HEAD
| 2 | Dati | LeafletMarkerMapInput, type_id, name, content, images — 3 Section (Luogo, Disservizio, Autore) |
=======
| 2 | Dati | LatitudeLongitudeInput, type_id, name, content, images — 3 Section (Luogo, Disservizio, Autore) |
>>>>>>> 83e0fe3 (Refactor CreateTicketWizardWidget to use LatitudeLongitudeInput with hidden label for improved UX. Update documentation to clarify the use of the new input component and its integration within the wizard flow.)
| 3 | Riepilogo | Infolist read-only strutturato + azione submit |

### Step 2 — Struttura Sezioni

```php
Wizard\Step::make('dati')
    ->schema([
        Section::make()->schema([
<<<<<<< HEAD
            // Luogo — LeafletMarkerMapInput (Geo module)
            LeafletMarkerMapInput::make('geo_location'),
=======
            // Luogo — LatitudeLongitudeInput (Geo module)
            LatitudeLongitudeInput::make('location'),
>>>>>>> 83e0fe3 (Refactor CreateTicketWizardWidget to use LatitudeLongitudeInput with hidden label for improved UX. Update documentation to clarify the use of the new input component and its integration within the wizard flow.)
        ]),
        Section::make()->schema([
            // Disservizio — tipo e descrizione
            Select::make('type_id'),
            Textarea::make('content'),
            FileUpload::make('images'),
        ]),
        Section::make()->schema([
            // Autore — read-only da auth user
        ]),
    ]);
```

### Regole Critiche del Wizard

**1. MAI gestione manuale step in Blade** — usare `Filament\Schemas\Components\Wizard`. Non creare tab/accordion manuali.

**2. Body plain** — il tag `<body>` deve essere SEMPRE senza classi e senza attributi (`html-body-parity-rule`).

**3. `@filamentScripts` obbligatorio** — CRITICAL nel layout Blade, senza di esso il wizard non funziona.

**4. `persistStepInQueryString('step')`** — usato solo in env locale/debug per QA; non in produzione.

<<<<<<< HEAD
**5. Geolocalizzazione via Geo module** — `LeafletMarkerMapInput` (mai implementazione custom nel wizard).
=======
**5. Geolocalizzazione via Geo module** — `LatitudeLongitudeInput` (mai implementazione custom nel wizard).
>>>>>>> 83e0fe3 (Refactor CreateTicketWizardWidget to use LatitudeLongitudeInput with hidden label for improved UX. Update documentation to clarify the use of the new input component and its integration within the wizard flow.)

**6. Multilingua obbligatoria** — tutto il testo usa chiavi `fixcity::...`; slug CMS via config (MAI hardcoded italiano nel PHP).

**7. `normalizeWizardFormState()`** — chiamato prima di `Ticket::query()->create()` allo step submit.

### Composizione CMS Pagina

```json
{
  "content_blocks": [
    {"type": "breadcrumb", "data": {"view": "fixcity::blocks.breadcrumb"}},
    {"type": "segnalazione-crea", "data": {"view": "fixcity::blocks.segnalazione-crea"}},
    {"type": "contacts-card", "data": {"view": "fixcity::blocks.contacts-card"}}
  ]
}
```

La view `segnalazione-crea` include `{{ $this->form }}` + `@filamentScripts`.

## Pannello Operatori (Admin Filament)

Il pannello operatori usa Filament Resources standard:
- `TicketResource` — CRUD ticket con relazioni
- `XotBaseCreateRecord` + pipeline `CreateRecord` (NON il wizard cittadino)
- Rotte admin: `/admin/tickets`, `/admin/tickets/create`, `/admin/tickets/{id}/edit`

## Dipendenze Cross-Module

| Modulo | Uso |
|--------|-----|
| `Xot` | `XotBaseWizardWidget`, `XotBaseModel`, `XotBaseServiceProvider` |
<<<<<<< HEAD
| `Geo` | `LeafletMarkerMapInput` per geolocalizzazione step 2 |
=======
| `Geo` | `LatitudeLongitudeInput` per geolocalizzazione step 2 |
>>>>>>> 83e0fe3 (Refactor CreateTicketWizardWidget to use LatitudeLongitudeInput with hidden label for improved UX. Update documentation to clarify the use of the new input component and its integration within the wizard flow.)
| `Sixteen` | CSS Design Comuni, stepper responsive, componenti AGID |
| `Cms` | `content_blocks` per composizione pagina frontoffice |
| `Lang` | `LangServiceProvider` — auto-label tutte le chiavi `fixcity::*` |
| `User` | `owner_id` — relazione ticket → utente |
| `Tenant` | Scoping multi-tenant automatico via Xot |

## Architettura

- **465 file PHP**, **58 classi/interfacce**
- PHPStan Level 10 ✅
- Multi-lingua: 50+ lingue supportate in `lang/`
- Ogni step wizard = funzione dedicata (clean-code-wizard-steps pattern)
- `GetViewByClassAction` per risoluzione view dinamica

## Cross-References

- [[../../../../../../laravel/Modules/Xot/docs/wiki/overviews/xot-module|Xot Module]] — XotBaseWizardWidget, XotBaseModel
<<<<<<< HEAD
- [[../../../../../../laravel/Modules/Geo/docs/wiki/index|Geo Module]] — LeafletMarkerMapInput
=======
- [[../../../../../../laravel/Modules/Geo/docs/wiki/index|Geo Module]] — LatitudeLongitudeInput
>>>>>>> 83e0fe3 (Refactor CreateTicketWizardWidget to use LatitudeLongitudeInput with hidden label for improved UX. Update documentation to clarify the use of the new input component and its integration within the wizard flow.)
- [[../../../../../../laravel/Themes/Sixteen/docs/wiki/overviews/sixteen-theme|Sixteen Theme]] — CSS Design Comuni
- [[../../../../../../laravel/Modules/Cms/docs/wiki/overviews/cms-module|Cms Module]] — content_blocks composizione

## Raw Sources Prioritari

- `README.md` — overview, wizard governance, regole critiche
- `ticket-wizard-frontoffice.md` — architettura wizard dettagliata, step content
- `wizard-governance-philosophy.md` — filosofia e politica wizard
- `filament-wizard-pattern.md` — pattern Filament Wizard
- `structure.md` — struttura directory, namespace, dipendenze
- `rules/filament-wizard-rules.md` — regole formali wizard
- `stories/` — story epiche per ogni feature wizard
