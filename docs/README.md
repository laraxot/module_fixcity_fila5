# 🎫 Modulo Fixcity - Sistema di Gestione Ticket

[![PHPStan Level 10](https://img.shields.io/badge/PHPStan-Level%2010-brightgreen.svg)](https://phpstan.org/)
[![Laravel 12.x](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com/)
[![Filament 5.x](https://img.shields.io/badge/Filament-5.x-blue.svg)](https://filamentphp.com/)
[![Translation Ready](https://img.shields.io/badge/Translation-IT%20%7C%20EN-green.svg)](https://laravel.com/docs/localization)

> **🚀 Modulo Fixcity**: Sistema completo per la gestione di ticket, segnalazioni e supporto tecnico con interfaccia Filament avanzata.

## 📋 Panoramica

Il modulo **Fixcity** è il sistema di ticketing dell'applicazione, fornendo:

- 🎫 **Gestione Ticket Completa** - Creazione, assegnazione e tracking ticket
- 👥 **Gestione Utenti e Ruoli** - Sistema di autorizzazione granulare
- 📊 **Dashboard e Reporting** - Statistiche e metriche avanzate
- 🔔 **Sistema Notifiche** - Notifiche real-time per aggiornamenti
- 🎨 **Interfaccia Filament** - UI moderna e responsive
- 🌐 **Multi-lingua** - Traduzioni complete IT/EN
- 🧙 **Wizard Frontoffice** - Creazione guidata unificata per i cittadini

## 🧙 Wizard Unificato (Segnalazione Crea)

Il modulo include un wizard unificato per la creazione di segnalazioni lato cittadino, che unifica le fasi di privacy, inserimento dati e riepilogo in un'unica esperienza fluida.

- **URL**: `/it/tests/segnalazione-crea`
- **Widget**: `CreateTicketWizardWidget`
- **Schema**: [Filament v5 `Wizard` + `Step`](https://filamentphp.com/docs/5.x/schemas/wizards) in `getFormSchema()`; base widget [`XotBaseWizardWidget`](../../Xot/docs/filament/widgets/xot-base-wizard-widget.md); `CreateTicketWizardWidget` usa `makeWizard()` per ereditare policy comuni Xot; vista Blade solo wrapper (titolo, contatti) + form (`{{ $this->form }}`)
- **Layout / parity**: Design Comuni (CSS tema Sixteen); story refactor completata: [7-34](../../../../_bmad-output/implementation-artifacts/7-34-create-ticket-wizard-filament-schema-wizard-refactor.md)
- **Privacy step semantics**: lo step 1 deve contenere copy GDPR first-class e non il solo checkbox; criterio di scelta componente e story: [7-47](../../../../_bmad-output/implementation-artifacts/7-47-segnalazione-crea-step1-privacy-notice-design-comuni-parity.md)
- **Data step semantics**: lo step 2 deve usare `Section` per la gerarchia visiva dei tre blocchi (`Luogo`, `Disservizio`, `Autore`) e `Infolist` solo per i dati read-only strutturati; story: [7-48](../../../../_bmad-output/implementation-artifacts/7-48-segnalazione-crea-step2-visual-parity-via-sections-and-infolist.md)

Per dettagli sull'architettura consulta: [Ticket Wizard Frontoffice](ticket-wizard-frontoffice.md) (`?step=` per QA; geolocalizzazione step 2 — story [7-33](../../../../_bmad-output/implementation-artifacts/7-33-segnalazione-crea-step2-geolocation-use-my-location-and-step-query.md)).

La creazione ticket lato **pannello** (operatori) segue le pagine resource Filament (`XotBaseCreateRecord`, pipeline `CreateRecord`): vedi [create-record-page](../../Xot/docs/filament/pages/create-record-page.md). Non è il flusso del wizard cittadino.

### ⚠️ Regole Critiche

**Filament Wizard Rule**: MAI gestione manuale step in Blade. Usa `Filament\Schemas\Components\Wizard`; la Blade resta wrapper e parity layer, non state machine. Vedi [Rules / Filament Wizard Rules](./rules/filament-wizard-rules.md).

**Body Plain Rule**: Il tag `<body>` deve essere SEMPRE plain — SENZA classi, SENZA attributi. Vedi [HTML Body Parity Rule](html-body-parity-rule.md).

**Route `/tests/[slug]` Rule**: per le pagine test Design Comuni lo scoping CSS/JS deve usare il wrapper canonico `.page-content[data-slug][data-side]`.

**Stepper Responsive**: Mobile-first con media queries. Vedi [Stepper Component](../../Themes/Sixteen/docs/design-comuni/stepper-component.md).

**Multilingua**: TUTTO il testo deve usare chiavi traduzione (`fixcity::...`) e gli slug di contenuto devono vivere in CMS/config — MAI hardcoded italiano nel PHP runtime.

**Clean Code Wizard Steps**: Ogni step = funzione dedicata. Vedi [Xot clean-code-wizard-steps](../../Xot/docs/clean-code-wizard-steps.md).

## ⚡ Funzionalità Core

### 🎫 **Ticket Management**
```php
// Creazione ticket con informazioni complete
$ticket = Ticket::create([
    'name' => 'Problema sistema',
    'content' => 'Descrizione dettagliata del problema',
    'priority' => TicketPriorityEnum::HIGH,
    'status' => TicketStatusEnum::OPEN,
    'type' => TicketTypeEnum::TECHNICAL,
    'owner_id' => $user->id,
]);
```

## 🎯 Stato Qualità

- **PHPStan**: Level 10 Compliance ✅
- **Test Coverage**: 85%+ ✅
- **Architettura**: Modular Monolith con Laraxot Base ✅

## 🚀 Quick Start

```bash
# Abilitare il modulo
php artisan module:enable Fixcity

# Eseguire le migrazioni
php artisan migrate

# Popolare dati di test
php artisan db:seed --class=FixcitySeeder
```

## 📚 Documentazione Completa

### 🏗️ Architettura
- [Struttura Modulo](structure.md) - Panoramica architettura
- [Modelli e Relazioni](models.md) - Documentazione modelli
- [Enum e Stati](enums.md) - Gestione stati e tipi
- [Componenti](components.md) - Componenti Blade e Filament
- [Links](links.md) - Link utili nel modulo

### 🎨 Filament & Wizard
- [Rules / Filament Wizard Rules](./rules/filament-wizard-rules.md) — ⚠️ REGOLA: MAI gestione manuale step in Blade, usa Filament Wizard
- [Wizard Governance Philosophy](wizard-governance-philosophy.md) - Perche/regola/visione/politica/zen su wizard
- [CreateTicketWizardWidget](CreateTicketWizardWidget.md) - Widget dettaglio
- [Ticket Wizard Frontoffice](ticket-wizard-frontoffice.md) - Architettura wizard
- [Resources](resources.md) - Gestione risorse Filament
- [Pages](pages.md) - Pagine personalizzate
- [Widgets](widgets.md) - Widget dashboard

### 📏 Regole & Standard
- [HTML Body Parity Rule](html-body-parity-rule.md) - Body plain, no classi
- [Rules / Filament Wizard Rules](./rules/filament-wizard-rules.md) - Wizard implementation pattern
- [Clean Code Wizard Steps](../../Xot/docs/clean-code-wizard-steps.md) - Step come funzioni (Xot)

### 🐛 PHPStan & Quality
- [PHPStan Fix Plan](phpstan-fix-plan.md) - Piano risoluzione errori
- [PHPStan Fixes](phpstan-fixes.md) - Fix applicati
- [PHPStan Immediate Fixes](phpstan-immediate-fixes.md) - Fix urgenti
- [PHPStan Level 10 Fixes](phpstan-level-10-fixes.md) - Fix level 10

### 🚀 Product & Planning
- [PRD](prd.md) - Product Requirements Document
- [Roadmap](roadmap/) - Roadmap del progetto
- [Strategy](strategy.md) - Strategia prodotto
- [Launch Plan](launch.md) - Piano di lancio
- [Sprint Planning](sprint.md) - Sprint correnti
- [User Research](research.md) - Ricerca utenti

## LLM Wiki Workflow

- Canonical wiki layer: [../../../../docs/wiki/README.md](../../../../docs/wiki/README.md)
- Compiled index: [../../../../docs/wiki/index.md](../../../../docs/wiki/index.md)
- Governance page: [../../../../docs/wiki/concepts/llm-wiki-governance.md](../../../../docs/wiki/concepts/llm-wiki-governance.md)

### 🔧 Technical
- [MCP Servers](MCP_SERVERS.md) - Server MCP configurati
- [Logging Performance](LOGGING_PERFORMANCE.md) - Performance logging
- [Boost Skill Fix](BOOST_SKILL_FIX_SUMMARY.md) - Fix Boost skill

### 🌐 Cross-Module Dependencies

| Module | Purpose | Link |
|--------|---------|------|
| **Geo** | Address field con geolocalizzazione | [Geo Address Field](../../Geo/docs/address-field-component.md) |
| **Xot** | Base classes (XotBaseWizardWidget) | [XotBaseWizardWidget](../../Xot/docs/filament/widgets/xot-base-wizard-widget.md) |
| **Sixteen** | Theme CSS, Design Comuni parity | [Sixteen Docs](../../Themes/Sixteen/docs/README.md) |

### 🌐 Traduzioni

Pattern: `fixcity::segnalazione.*`
File: `lang/{locale}/segnalazione.php`

## 📞 Support & Maintainers

- **🏢 Team**: Laraxot Development Team
- **📧 Email**: fixcity@laraxot.com

---

**🔄 Ultimo aggiornamento**: 9 Aprile 2026
**📦 Versione**: 2.0.0
**🐛 PHPStan Level**: 10 ✅
**🌐 Translation Standards**: IT/EN complete ✅
**✨ Filament 5.x**: Integrato e funzionante ✅
