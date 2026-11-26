---
title: "Story: Remove ticket logic from PageSlugMiddleware — ADR 0010"
type: story
status: accepted
created: 2026-11-23
updated: 2026-11-23
epic: "Fixcity CMS ownership"
issues: []
discussions: []
related:
  - docs/wiki/rules/folio-page-shell-no-props-extends.md
  - docs/wiki/memories/0001-cms-owns-page-resolution.md
---

# Story: Remove ticket logic from PageSlugMiddleware — ADR 0010

## Descrizione

Rimuovere la logica dei ticket dal middleware `PageSlugMiddleware` del modulo Cms come stabilito da ADR 0010. La risoluzione delle pagine Folio deve affidarsi ai blocchi CMS JSON e alle route namespaziate, non a middleware accoppiati al dominio Ticket.

## Obiettivo

- Separare chiaramente le responsabilità tra Cms e Fixcity
- Eliminare la dipendenza di `PageSlugMiddleware` da `Ticket` model/enums
- Garantire che il reindirizzamento `/ticket/{code}` → `/segnalazione/{code}` sia gestito da `RedirectTicketTrackCodeMiddleware` nel modulo Fixcity

## Scope

- In scope: `PageSlugMiddleware` (Cms), `RedirectTicketTrackCodeMiddleware` (Fixcity), CMS JSON blocks
- Out of scope: migrazione di eventuali route web.php esistenti (gestione separata)

## Conferma

- [ ] `PageSlugMiddleware` non contiene logica ticket/folio reindirizzamento (già verificato)
- [ ] `RedirectTicketTrackCodeMiddleware` in Fixcity gestisce `/ticket/{code}` → `/segnalazione/{code}` 
- [ ] Configurazione route: `config/local/fixcity/database/content/pages/home.json` per le pagine statiche
- [ ] Test: `HeaderAreaPersonaleLinksContractTest.php` passa per i link ticket
- [ ] ADR 0010 creato e linkato nell'indice `README.md` del modulo Xot (già fatto)

## GitHub (tracciamento)

- Issue: DA CREARE sul repo `laraxot/module_fixcity_fila5`
- Comando: `gh issue create --title "ADR 0010: Remove ticket logic from PageSlugMiddleware" --body "Vedi ADR in laravel/Modules/Xot/docs/adr/0010-no-ticket-logic-in-page-slug-middleware.md"`