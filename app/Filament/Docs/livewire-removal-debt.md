---
title: debito tecnico — rimozione livewire ticketlist
type: debt
module: fixcity
updated: 2026-05-29
related:
  - ./frontoffice-no-standalone-livewire.md
---

# Rimozione `Livewire\TicketList`

## Completato

- File `app/Livewire/TicketList.php` eliminato
- Registrazione orfana `View/Components/_components.json` → `[]`
- Homepage `/it` su CMS `ticket-layout` + `ticket.layout`

## Residui da non confondere

| File | Ruolo |
|------|--------|
| `Filament/Blocks/TicketListBlock.php` | Builder CMS tipo `ticket_list` (legacy), non il componente Livewire |
| `lang/*/approvel_ticket_list` | Chiavi admin, non frontoffice |

## Verifica

```bash
rg -l 'Livewire\\\\TicketList|@livewire.*TicketList' laravel/
# atteso: nessun match runtime
```
