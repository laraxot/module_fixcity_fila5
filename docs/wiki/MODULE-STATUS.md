---
title: "Fixcity Module - Status & TODO"
type: "status"
tags: ["fixcity", "status", "todo", "core-business"]
date: "2026-06-13"
qmd: "fixcity-status"
github_issue: ""
github_discussion: ""
---

# Fixcity Module - Status & TODO

## Overview

Modulo core business per la gestione segnalazioni cittadini (FixCity pattern).
Stato: 🔄 In sviluppo attivo - 60% completato

## Componenti Implementate

### ✅ Completate

| Componente | Stato | Note |
|------------|-------|------|
| Models | ✅ | BaseModel, Segnalazione, Categoria |
| Migrations | ✅ | Schema completo |
| Actions base | ✅ | CRUD operations |
| Filament Resource | 🔄 | Basic resource funzionante |

### 🔄 In Corso

| Componente | Stato | Bloccati da | Priorità |
|------------|-------|-------------|----------|
| Geocoding | 🔄 | Geo module integration | Alta |
| Notifiche | 🔄 | Notify module | Media |
| Workflow | 🔄 | Activity module events | Alta |
| API public | 📝 | Documentazione | Media |

### 📝 Da Fare

| Componente | Stato | Priorità |
|------------|-------|----------|
| Import bulk | 📝 | Bassa |
| Export reports | 📝 | Media |
| Dashboard analytics | 📝 | Media |
| Mobile API | 📝 | Alta |

## Dipendenze Moduli

```
Fixcity
├── Xot (core) ✅
├── Geo (geocoding) 🔄
├── Activity (audit) 🔄
├── Notify (alerts) 🔄
├── User (auth) ✅
└── Tenant (multi-tenant) ✅
```

## Test Status

- **Unit tests**: 40% coverage
- **Feature tests**: 20% coverage
- **PHPStan**: ❌ Errori da risolvere

## File da Completare

### Actions (app/Actions/)
- [ ] `AssignSegnalazioneAction.php` - Assegnazione automatica
- [ ] `EscalateSegnalazioneAction.php` - Escalation workflow
- [ ] `NotifyCittadinoAction.php` - Notifiche cittadino
- [ ] `CalculatePriorityAction.php` - Calcolo priorità ML

### Filament (app/Filament/)
- [ ] Widget `SegnalazioniStatsWidget.php`
- [ ] RelationManager `AllegatiRelationManager.php`
- [ ] Custom Action `EscalateAction.php`

### API (resources/views/pages/api/)
- [ ] `segnalazioni/create.blade.php` + Action
- [ ] `segnalazioni/list.blade.php` + Action
- [ ] `segnalazioni/show.blade.php` + Action

## Pattern da Seguire

### 1. Actions Architecture
```php
namespace Modules\Fixcity\Actions\Segnalazione;

use Modules\Xot\Actions\Filament\Action;

class CreateSegnalazioneAction
{
    public function execute(array $data): Segnalazione
    {
        // Validazione
        // Geocoding se coordinate mancanti
        // Creazione
        // Notifica
        // Audit log
    }
}
```

### 2. Filament Resource
```php
class SegnalazioneResource extends XotBaseResource
{
    protected static ?string $model = Segnalazione::class;
    
    public static function getRelations(): array
    {
        return [
            AllegatiRelationManager::class,
            StoricoRelationManager::class,
        ];
    }
}
```

### 3. Folio API Pages
```
resources/views/pages/api/segnalazioni/
├── index.blade.php (lista)
├── create.blade.php (form)
├── show.blade.php (dettaglio)
└── update.blade.php (modifica)
```

## Collegamenti

- [Project Roadmap](../../Activity/docs/wiki/PROJECT-ROADMAP.md)
- [Xot PHPStan Best Practices](../../Xot/docs/wiki/PHPSTAN-BEST-PRACTICES.md)
- [No Controllers Rule](../../../docs/wiki/rules/no-controllers-rule.md)
