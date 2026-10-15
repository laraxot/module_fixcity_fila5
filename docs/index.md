---
title: "index"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-26
qmd: "index"
issues: []
discussions: []
status: draft
---

# Fixcity Module

Sistema completo per la gestione di ticket, segnalazioni e supporto tecnico con interfaccia Filament avanzata e wizard unificato cittadino.

## Overview

Il modulo **Fixcity** è il sistema di ticketing dell'applicazione:

- 🎫 **Gestione Ticket Completa** - Creazione, assegnazione, tracking e closure ticket
- 👥 **Gestione Utenti e Ruoli** - Sistema autorizzazione granulare con policy
- 📊 **Dashboard e Reporting** - Statistiche e metriche (widget KPI/SLA/rating) ed export CSV/XLSX; _(correzione 2026-10-14: non esiste alcun report PDF, i riferimenti a `pdf` in `app/` sono solo i MIME degli allegati)_
- 🔔 **Sistema Notifiche** - Notifiche di assegnazione e cambio stato su canali `mail` e `database` (`config/ticket.php`, `app/Notifications/`); _(correzione 2026-10-14: non sono real-time, `app/Broadcasting/` e' vuota)_
- 🎨 **Interfaccia Filament** - UI moderna responsive, Filament 5.x
- 🌐 **Multi-lingua** - Traduzioni complete IT/EN
- 🧙 **Wizard Frontoffice** - Creazione unificata guidata per cittadini

## Key Features

### Ticket Management
- Creazione, modifica, assegnazione ticket
- Tracking stato avanzato (`TicketStatusEnum`, 9 casi: draft, pending, in_review, in_progress, on_hold, resolved, closed, reopened, open)
- Priorità (`TicketPriorityEnum`, 5 casi: low, medium, high, critical, urgent)
- Categorie/tipi di segnalazione
- Attachment support (allegati immagini/file)

### Frontoffice Wizard
- Creazione guidata unificata per cittadini
- **URL**: `/it/tickets/create` (Folio `resources/views/pages/tickets/create.blade.php`, route `tickets.create`, accesso con `auth`). _(Correzione 2026-10-14: la vecchia `/it/tests/segnalazione-crea` non e' piu' l'entrypoint, vedi [ticket-wizard-frontoffice.md](ticket-wizard-frontoffice.md))_
- **Widget**: `CreateTicketWizardWidget` (`app/Filament/Widgets/`), schema in `TicketForm::getSteps()`, chiavi dei passi `privacy`, `data`, `summary`
- **Architettura**: Filament v5 Wizard + Step pattern
- **Steps**:
  1. **Privacy Notice** - GDPR compliance first-class (non solo checkbox)
  2. **Dati Segnalazione** - Sezioni (Luogo, Disservizio, Autore) + geolocalizzazione
  3. **Riepilogo** - TextEntry per review pre-submit

### Dashboard & Reporting
- Dashboard operatori con KPI
- Report statistici (PDF, Excel)
- Filtri avanzati (data, status, categorie)
- Export funzionalità

### User & Role Management
- Operatori admin, supervisori, operatori standard
- Cittadini registrati vs anonimi
- Policy autorizzazione granulari per actions

## Architecture

```
Fixcity/
├── app/
│   ├── Models/
│   │   ├── Ticket.php
│   │   ├── TicketActivity.php, TicketComment.php, TicketHour.php
│   │   ├── TicketRelation.php, TicketSubscriber.php
│   │   └── Category.php, Profile.php, User.php, Activity.php
│   ├── Enums/                 (TicketStatusEnum, TicketPriorityEnum, TicketTypeEnum, ...)
│   ├── Actions/               (64 file, QueueableAction)
│   │   ├── CreateTicketAction.php
│   │   └── ChangeStatus.php
│   ├── Filament/
│   │   ├── Resources/TicketResource.php (+ Pages/, Schemas/, Tables/, RelationManagers/)
│   │   ├── Pages/Dashboard.php
│   │   ├── Exports/TicketExporter.php
│   │   └── Widgets/           (CreateTicketWizardWidget, TicketOverview, ...)
│   ├── Livewire/, Notifications/, Policies/, Observers/, Events/, Listeners/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── lang/                      (80 file PHP in en, 75 in it; json per molte altre lingue)
├── resources/views/pages/     (Folio: tickets/*, api/*, home, lista-categorie)
└── docs/
```

_(Albero corretto il 2026-10-14 con `ls` del codice. Prima elencava `UpdateTicketAction.php` e i modelli `TicketStatus`, `TicketPriority`, `TicketType`, che non esistono: sono enum in `app/Enums/`.)_

**Base Classes**: `XotBaseModel`, `XotBaseCreateRecord`, `XotBaseWizardWidget`

**Critical Rules**:
- **Filament Wizard Rule**: MAI gestione manuale step in Blade — usa `Filament\Schemas\Components\Wizard`
- **Body Plain Rule**: Tag `<body>` SEMPRE plain (no classes, no attributes)
- **Route `/tests/[slug]`**: CSS scoping con `.page-content[data-slug][data-side]`
- **Multilingua**: TUTTO testo via chiavi traduzione (`fixcity::...`), no hardcoded italiano

## Core Components

### Models
- **Ticket** - Modello principale segnalazioni
- **TicketStatusEnum** - 9 stati: draft, pending, in_review, in_progress, on_hold, resolved, closed, reopened, open
- **TicketPriorityEnum** - 5 priorità: low, medium, high, critical, urgent
- **TicketTypeEnum** - tipi di segnalazione civica (es. road_maintenance, public_lighting, waste_collection, parks_and_gardens, sewage_and_drainage, public_transport, complaint, suggestion, other; elenco completo in `app/Enums/TicketTypeEnum.php`). _(Correzione 2026-10-14: i valori `bugReport`, `featureRequest`, `support` non esistono)_

### Filament Resources
- **TicketResource** (`app/Filament/Resources/TicketResource.php`) - Gestione admin ticket, pannello su `/fixcity/admin/tickets`
- **ListTickets** - Elenco con filtri e bulk actions (`TicketsTable`)
- **CreateTicket** - Creazione ticket operatori
- **EditTicket**, **ViewTicket** - Modifica e dettaglio (schema in `TicketForm`, `TicketInfolist`)

### Widgets
- **CreateTicketWizardWidget** - Wizard frontoffice cittadini
- **Dashboard** (`app/Filament/Pages/Dashboard.php`) - pagina del pannello admin. _(Correzione 2026-10-14: non esiste un `DashboardWidget`)_
- **TicketOverview** — KPI volumi (totali, backlog, in lavorazione, risolte) — STORY-025/040
- **TicketSlaOverviewWidget** — tempi medi risoluzione — STORY-041
- **CitizenRatingOverviewWidget** — aggregato rating cittadino — STORY-044

### Actions
- `CreateTicketAction` - Logica creazione ticket
- `ChangeStatus` (`app/Actions/ChangeStatus.php`) - Cambio status; `ChangeTicketPriorityAction` - cambio priorità. _(Correzione 2026-10-14: `UpdateTicketStatusAction` non esiste)_
- `GetTicketKpiAggregateAction` / `GetTicketSlaMetricsAction` — analytics dashboard
- `TicketExporter` (Filament) — export CSV/XLSX lista ticket con filtri tabella — STORY-024

## Implementation Guide

### Quick Start
```bash
# Abilitare il modulo
php artisan module:enable Fixcity

# Eseguire migrazioni
php artisan migrate

# Seeder dati demo (solo ambienti local, testing, demo)
php artisan db:seed --class='Modules\Fixcity\Database\Seeders\FixcityDatabaseSeeder'
```

_(Correzione 2026-10-14: la classe `FixcitySeeder` non esiste; i seeder sono `FixcityDatabaseSeeder` e `FixcityDemoSeeder` in `database/seeders/`. Il comando non e' stato eseguito perché scrive sul database.)_

### Creazione Ticket (Backend)
```php
$ticket = Ticket::create([
    'name' => 'Problema sistema',
    'content' => 'Descrizione dettagliata',
    'priority' => TicketPriorityEnum::HIGH,
    'status' => TicketStatusEnum::OPEN,
    'type' => TicketTypeEnum::ROAD_MAINTENANCE,
    'owner_id' => $user->id,
]);
```

_(Correzione 2026-10-14: `TicketTypeEnum::BUGREPORT` non esiste. `name`, `content`, `priority`, `status`, `type` sono in `$fillable` di `Ticket`; per la creazione dal front office si usa `CreateTicketAction`, con i dati normalizzati da `NormalizeTicketLocationDataAction`.)_

### Wizard Frontoffice
- **Layout**: Design Comuni (CSS tema Sixteen)
- **Geolocalizzazione**: Step 2 con "Use My Location" button
- **Query Parameter**: `?step=N` per QA e testing direct step access
- **Privacy**: Step 1 GDPR notice (story 7-47) non optional
- **Visual Parity**: Section+Infolist pattern (story 7-48)

Vedi dettagli: [Ticket Wizard Frontoffice](ticket-wizard-frontoffice.md)

## Best Practices

> Nota 2026-10-14: questa sezione e' un elenco di indicazioni generiche. Non e' stata verificata nel codice (paginazione a 20, indici su `status`/`priority`/`created_at`, cache dei KPI, auto-assegnazione per categoria): nella migrazione `2026_06_10_140000_create_tickets_table.php` l'unico `->index()` esplicito e' su `type_id`. Trattarla come desiderata, non come descrizione.

### Ticket Creation
- Validate input data (name required, content non-empty)
- Auto-assign based on category rules
- Sanitize content HTML
- Store geolocation quando disponibile

### Status Management
- Workflow: open → in_progress → resolved → closed
- Avoid status jumps (validation on transitions)
- Log status changes via Activity module
- Notify user su ogni cambio status

### Performance
- Paginate ticket lists (20 per page default)
- Eager load relazioni (owner, assignee, comments)
- Index su status, priority, created_at
- Cache dashboard KPI

### Localization
- Enum strings via config translation
- Template strings via lang files
- Wizard step labels sempre da `fixcity::` namespace
- Date format rispetto locale user

## Related Modules

- [User Module](../../User/docs) - Utenti e autenticazione
- [Comment Module](../../Comment/docs) - Commenti su ticket
- [Activity Module](../../Activity/docs) - Activity logging
- [Notify Module](../../Notify/docs) - Notifiche
- [Xot Module](../../Xot/docs) - Base classes e patterns

## Troubleshooting

**Wizard steps non visibili su mobile**
- Verificare media queries in [Stepper Component](../../../Themes/Sixteen/docs/design-comuni/stepper-component.md)
- Check CSS scoping `.page-content[data-slug]`

**Geolocalizzazione non funziona**
- Verificare HTTPS (required per browser geolocation API)
- Check browser permissions dialogo
- Vedi story 7-33 (segnalazione-crea step 2, geolocalizzazione e `?step=`) _(target mancante, verificato 2026-10-14: `_bmad-output/` non esiste nel repo)_; sul comportamento attuale della mappa senza HTTPS vedi [DCP-146](stories/dcp-146-wizard-passo-dati-mappa-sempre-funzionante.story.md) e [DCP-127 (Geo)](../../Geo/docs/stories/dcp-127-mappa-centratura-senza-https.story.md)

**Notifiche non inviate su status change**
- Verificare event listeners sono registered
- Check queue job status: `php artisan queue:failed`
- Prova di riferimento: `tests/Feature/Actions/TicketStatusNotificationTest.php`; l'invio parte da `NotifyTicketSubscribersOfStatusChangeAction`. _(Correzione 2026-10-14: il comando precedente `Ticket::find(1)->notify(new TicketStatusChangedNotification())` non funziona: `Ticket` non usa `Notifiable` e il costruttore richiede `string $ticketCode` e `TicketStatusEnum $status`)_

**Performance lento su ticket list con molti records**
- Aggiungere pagination se assente
- Verify indexes su database
- Implement query caching per filter options

## Documentation

Vedi anche:
- [README](README.md) - Panoramica module
- [Wizard Governance Philosophy](wizard-governance-philosophy.md) - Zen del wizard
- [CreateTicketWizardWidget](CreateTicketWizardWidget.md) - Widget dettagli
- [Ticket Wizard Frontoffice](ticket-wizard-frontoffice.md) - Architettura wizard
- [Filament Wizard Rules](./rules/filament-wizard-rules.md) - Regole strict ⚠️
- [HTML Body Parity Rule](html-body-parity-rule.md) - Body plain rule
- [Module Boundary Philosophy](MODULE-BOUNDARY-PHILOSOPHY.md) - Scope del modulo
- [Filament Guidelines](filament-components-guidelines.md) - Pattern Filament

---
**Status**: Active Development  
**PHPStan Level**: Level 10 ✅  
**Translation**: IT/EN ✅  
**Wizard Status**: Production Ready  
**Last Updated**: 2026-05-13

> Verifica 2026-10-14 di questo blocco: `./vendor/bin/phpstan analyse Modules/Fixcity` (config unica `laravel/phpstan.neon`, livello `max`) dà 122 errori, tutti in `config/*.php`, e 0 in `app/`, `tests/` e `database/`; quindi "Level 10 ✅" vale per il codice ma non per il modulo intero. "Production Ready" non e' verificato: la [matrice di conformita'](compliance/01-matrice-conformita-normativa.md) (stato `draft-for-dpo-legal-review`, 2026-10-13) elenca lacune di gravita' alta ancora aperte (per esempio N-02 consenso privacy non persistito, N-24 SPID/CIE assenti). "IT/EN" e' riduttivo: `lang/` ha PHP per piu' lingue (80 file in `en`, 75 in `it`, 35 in `es`, 32 in `de`). Stato aggiornato: [README.md](README.md), sezione Fixcity.
<<<<<<< .merge_file_yT4qCI

--- BMAD + Second Brain ---
- Scopo: documentazione tecnica del modulo.
- Utilità: riferimento sviluppatori/audit.
- Funzionalità: struttura, dipendenze, convenzioni (no date .md, git avanti).
=======
>>>>>>> .merge_file_gTG5aB
