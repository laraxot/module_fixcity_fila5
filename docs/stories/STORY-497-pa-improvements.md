---
created: 2026-09-26
updated: 2026-09-26
qmd: "STORY 497 pa improvements"
issues: []
discussions: []
title: "STORY-497: PA/Backoffice Improvements"
type: story
status: open
priority: Must
issue_link: "https://github.com/laraxot/fixcity/issues/497"
discussion_link: "https://github.com/laraxot/fixcity/discussions/497"
tags: [pa, backoffice, filament, rbac]
---

# STORY-497 — PA/Backoffice Improvements

## Goal
Migliora il backoffice Filament per gli operatori PA: filtri avanzati, assegnazione, RBAC completo, notifiche.

## Required Implementation

### 1. Filament Resource Enhancements
- `TicketResource/Tables/TicketsTable.php` - Filtri avanzati (data, categoria, priorità, ecc.)
- `TicketResource/Pages/` - Personalizzazioni pagine list/edit/view

### 2. Actions (app/Actions/Filament/Tickets/)
- Enhanced `AssignTicketAction.php` - Con notifiche e logging
- `EscalateTicketAction.php` - Escalation ticket a supervisor
- `AddTicketCommentAction.php` - Aggiungere commenti interni/esterni

### 3. Policies (app/Models/Policies/)
- Enhanced `TicketPolicy.php` - Controlli più granularizzati per ruolo
- `TicketCommentPolicy.php` - Policy per commenti ticket

### 4. Notifications (app/Notifications/)
- `TicketAssignedNotification.php` - Quando assegnato a operatore
- `TicketStatusChangedNotification.php` - Cambiamento stato (già parzialmente esistente)
- `TicketCommentedNotification.php` - Nuovo commento sul ticket

## Issue Link
- GitHub Issue #497
- Discussion #497

## Evidence Post-Fix Checklist
- [ ] TicketResource filtri avanzati implementati
- [ ] Enhanced AssignTicketAction con notifiche
- [ ] Enhanced TicketPolicy con controlli ruolo
- [ ] Notification classes create e testate
- [ ] Integration completa con Filament
