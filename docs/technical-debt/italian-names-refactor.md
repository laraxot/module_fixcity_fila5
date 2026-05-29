---
title: "Technical Debt: Italian Names in Code"
type: decision
confidence: high
created: 2026-05-29
updated: 2026-05-29
tags: [technical-debt, refactoring, naming, i18n]
related:
  - ../../../../../docs/wiki/rules/no-italian-folder-names-in-code.md
---

# Debito Tecnico: Nomi Italiani nel Codice

## Stato

**Correzione immediata**: ✅ COMPLETATA  
**Refactoring completo**: 🔄 IN CORSO

## Cosa è stato corretto (2026-05-29)

### Componenti Blade (CRITICO - ✅ FATTO)
- ✅ Cartella: `Themes/Sixteen/components/blocks/segnalazioni/` → `ticket/`
- ✅ File spostati: layout, filters-sidebar, tabs, heading, modal-disservizio, map-preview
- ✅ Riferimenti @include aggiornati in layout.blade.php
- ✅ Commento in TicketList.php aggiornato

## Refactoring Rimasto

### Classi PHP (ALTA PRIORITÀ)

| Classe | Nuovo Nome | File |
|--------|-----------|------|
| `SegnalazioniFilterViewModel` | `TicketFilterViewModel` | `app/ViewModels/SegnalazioniFilterViewModel.php` |
| `SegnalazioniFilterViewModelTest` | `TicketFilterViewModelTest` | `tests/Unit/ViewModels/SegnalazioniFilterViewModelTest.php` |
| `BuildSegnalazioniFilterAggregateAction` | `BuildTicketFilterAggregateAction` | `app/Actions/BuildSegnalazioniFilterAggregateAction.php` |

### Riferimenti da Aggiornare

```bash
# Cerca tutti i riferimenti da aggiornare
grep -r "SegnalazioniFilterViewModel" laravel/Modules/Fixcity --include="*.php" --include="*.blade.php"
grep -r "BuildSegnalazioniFilterAggregateAction" laravel/Modules/Fixcity --include="*.php"
```

## Piano di Migrazione

### Fase 1: Creare nuove classi (mantenere compatibilità)
1. Creare `TicketFilterViewModel.php` con nuovo nome
2. Creare `BuildTicketFilterAggregateAction.php` con nuovo nome
3. Fare extends/alias dalle vecchie classi per retrocompatibilità

### Fase 2: Aggiornare riferimenti
1. Aggiornare `layout.blade.php` (usa `SegnalazioniFilterViewModel`)
2. Aggiornare altri file che usano queste classi

### Fase 3: Deprecare vecchie classi
1. Aggiungere `@deprecated` PHPDoc alle vecchie classi
2. Mantenere per 1-2 sprint

### Fase 4: Rimuovere
1. Rimuovere vecchie classi dopo il periodo di deprecazione

## Comandi di Verifica

```bash
# Verifica correzione immediata
bash bashscripts/ai/check-italian-names-in-code.sh

# Verifica refactoring completo
bash bashscripts/ai/check-italian-names-full.sh  # TODO: creare questo script
```

## Regola di Prevenzione

**Documentazione**: [docs/wiki/rules/no-italian-folder-names-in-code.md](../../../../../docs/wiki/rules/no-italian-folder-names-in-code.md)

**Checklist prima di creare file/classi**:
- [ ] Il nome è in inglese?
- [ ] Corrisponde al Model (es. `Ticket`)?
- [ ] Non è una traduzione italiana?

## Changelog

- **2026-05-29**: Correzione cartelle componenti Blade (ticket/)
- **2026-05-29**: Creata documentazione e regola permanente
- **2026-05-29**: Creata memoria persistente e script di verifica
