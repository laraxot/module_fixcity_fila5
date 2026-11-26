---
title: "Filament ViewTicket — etichette ciclo segnalazione"
type: story
status: done
module: Fixcity
tags: [bmad, filament, blueprint, quality, i18n]
qmd: "viewticket changeStatus changePriority etichette operatore ciclo ticket filament blueprint"
related:
  - ../bmad/filament-blueprint.md
  - ../bmad/ticket-lifecycle-filament-blueprint.md
  - ../compliance/00-scopo-del-progetto.md
---

# Scopo

FixCity serve al cittadino che segnala e all'operatore che prende in carico.
Sulla scheda Filament `ViewTicket` l'operatore deve vedere etichette italiane
chiare (approva, scarta, risolvi, comunica), non chiavi grezze `fixcity::ticket.actions.*`.

# Causa

`lang/it/ticket.php` aveva stub (`label => changeStatus`) e mancavano le chiavi
usate da `ViewTicket` (`approve`, `reject`, `resolve`, `close`, `send_communication`, …).
`ChangeStatus` / `ChangePriority` avevano stringhe hardcoded (anti-pattern blueprint).

# Fix

- Arricchite chiavi IT in `lang/it/ticket.php` + EN in `lang/en/ticket_actions.php`
- `ChangeStatus` / `ChangePriority`: solo `__()` / `translateLabel()`, niente hardcoded
- Rimossi orphan morti: `Resources/RelationManagers/CommentsRelationManager`, due `SimpleWidget` senza chiamanti

# Verifica

```bash
cd laravel && php -r '… __("fixcity::ticket.actions.approve.label") …'
./vendor/bin/phpstan analyse Modules/Fixcity --no-progress
```
