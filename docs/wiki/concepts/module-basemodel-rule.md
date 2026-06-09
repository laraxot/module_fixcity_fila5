---
title: Modelli Fixcity — estendere BaseModel del modulo
type: concept
created: 2026-05-29
tags: [basemodel, fixcity, xot, architecture]
related:
  - ../../../../docs/wiki/guidelines/module-basemodel-pattern.md
---

# Modelli Fixcity — `BaseModel` del modulo

## Regola

| Classe | Estende |
|--------|---------|
| `Modules\Fixcity\Models\BaseModel` | `Modules\Xot\Models\XotBaseModel` |
| Ogni modello dominio Fixcity (es. `Ticket`) | `Modules\Fixcity\Models\BaseModel` |

**Vietato:** `class Ticket extends XotBaseModel` nel modulo Fixcity.

## Perché

- Connection `fixcity`, `SoftDeletes`, cast Updater centralizzati in un solo punto
- Confine modulo: customizzazioni Fixcity non contaminano Xot
- Allineamento [module-basemodel-pattern.md](../../../../docs/wiki/guidelines/module-basemodel-pattern.md)

## Checklist agente

- [ ] Nuovo model in `app/Models/` → `extends BaseModel`
- [ ] PHPStan L10 dopo edit
- [ ] Nessun `use Modules\Xot\Models\XotBaseModel` nei model concreti del modulo
