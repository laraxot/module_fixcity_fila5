# Fixcity Module

Sistema completo per la gestione di ticket, segnalazioni e supporto tecnico con interfaccia Filament avanzata e wizard unificato cittadino.

## Overview

Il modulo **Fixcity** è il sistema di ticketing dell'applicazione:

- 🎫 **Gestione Ticket Completa** - Creazione, assegnazione, tracking e closure ticket
- 👥 **Gestione Utenti e Ruoli** - Sistema autorizzazione granulare con policy
- 📊 **Dashboard e Reporting** - Statistiche, metriche avanzate e report PDF
- 🔔 **Sistema Notifiche** - Notifiche real-time per aggiornamenti status
- 🎨 **Interfaccia Filament** - UI moderna responsive, Filament 5.x
- 🌐 **Multi-lingua** - Traduzioni complete IT/EN
- 🧙 **Wizard Frontoffice** - Creazione unificata guidata per cittadini

## Key Features

### Ticket Management
- Creazione, modifica, assegnazione ticket
- Tracking stato avanzato (open, in progress, resolved, closed)
- Priorità (low, medium, high, critical)
- Categorie/tipi di segnalazione
- Attachment support (allegati immagini/file)

### Frontoffice Wizard
- Creazione guidata unificata per cittadini
- **URL**: `/it/tests/segnalazione-crea`
- **Widget**: `CreateTicketWizardWidget`
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
│   │   ├── TicketStatus.php
│   │   ├── TicketPriority.php
│   │   └── TicketType.php
│   ├── Actions/
│   │   ├── CreateTicketAction.php
│   │   └── UpdateTicketAction.php
│   ├── Filament/
│   │   ├── Resources/
│   │   ├── Pages/
│   │   └── Widgets/
│   │       └── CreateTicketWizardWidget.php
│   └── Events/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── lang/
│   ├── it/
│   └── en/
└── docs/
```

**Base Classes**: `XotBaseModel`, `XotBaseCreateRecord`, `XotBaseWizardWidget`

**Critical Rules**:
- **Filament Wizard Rule**: MAI gestione manuale step in Blade — usa `Filament\Schemas\Components\Wizard`
- **Body Plain Rule**: Tag `<body>` SEMPRE plain (no classes, no attributes)
- **Route `/tests/[slug]`**: CSS scoping con `.page-content[data-slug][data-side]`
- **Multilingua**: TUTTO testo via chiavi traduzione (`fixcity::...`), no hardcoded italiano

## Core Components

### Models
- **Ticket** - Modello principale segnalazioni
- **TicketStatusEnum** - Stati: open, in_progress, resolved, closed
- **TicketPriorityEnum** - Priorità: low, medium, high, critical
- **TicketTypeEnum** - Tipi segnalazione (bugReport, featureRequest, support)

### Filament Resources
- **TicketResource** - Gestione admin ticket
- **TicketListPage** - Elenco con filtri e bulk actions
- **TicketCreatePage** - Creazione ticket operatori
- **TicketEditPage** - Modifica e tracking status

### Widgets
- **CreateTicketWizardWidget** - Wizard frontoffice cittadini
- **DashboardWidget** - KPI e statistiche
- **TicketStatsWidget** - Contatori per status/priorità

### Actions
- `CreateTicketAction` - Logica creazione ticket
- `UpdateTicketStatusAction` - Cambio status
- `ExportTicketReportAction` - Export PDF/Excel

## Implementation Guide

### Quick Start
```bash
# Abilitare il modulo
php artisan module:enable Fixcity

# Eseguire migrazioni
php artisan migrate

# Seeder dati test
php artisan db:seed --class=FixcitySeeder
```

### Creazione Ticket (Backend)
```php
$ticket = Ticket::create([
    'name' => 'Problema sistema',
    'content' => 'Descrizione dettagliata',
    'priority' => TicketPriorityEnum::HIGH,
    'status' => TicketStatusEnum::OPEN,
    'type' => TicketTypeEnum::BUGREPORT,
    'owner_id' => $user->id,
]);
```

### Wizard Frontoffice
- **Layout**: Design Comuni (CSS tema Sixteen)
- **Geolocalizzazione**: Step 2 con "Use My Location" button
- **Query Parameter**: `?step=N` per QA e testing direct step access
- **Privacy**: Step 1 GDPR notice (story 7-47) non optional
- **Visual Parity**: Section+Infolist pattern (story 7-48)

Vedi dettagli: [Ticket Wizard Frontoffice](ticket-wizard-frontoffice.md)

## Best Practices

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

- [User Module](../User/docs/) - Utenti e autenticazione
- [Comment Module](../Comment/docs/) - Commenti su ticket
- [Activity Module](../Activity/docs/) - Activity logging
- [Notify Module](../Notify/docs/) - Notifiche
- [Xot Module](../Xot/docs/) - Base classes e patterns

## Troubleshooting

**Wizard steps non visibili su mobile**
- Verificare media queries in [Stepper Component](../../Themes/Sixteen/docs/design-comuni/stepper-component.md)
- Check CSS scoping `.page-content[data-slug]`

**Geolocalizzazione non funziona**
- Verificare HTTPS (required per browser geolocation API)
- Check browser permissions dialogo
- Vedi story [7-33](../../../../_bmad-output/implementation-artifacts/7-33-segnalazione-crea-step2-geolocation-use-my-location-and-step-query.md)

**Notifiche non inviate su status change**
- Verificare event listeners sono registered
- Check queue job status: `php artisan queue:failed`
- Teste manualmente: `Ticket::find(1)->notify(new TicketStatusChangedNotification())`

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
