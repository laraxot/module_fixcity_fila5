---
title: "Contratto — TicketPolicy"
type: contract
action: "Modules\\Fixcity\\Policies\\TicketPolicy"
module: Fixcity
status: accepted
tags: [contract, policy, autorizzazione, ticket, permessi]
created: 2026-11-26
updated: 2026-11-26
---

# TicketPolicy

Sorgente: `app/Policies/TicketPolicy.php` (+ `BasePolicy.php`, che definisce `PA_ROLES` e `before()`). E' l'autorita' unica su chi
puo' fare cosa a una segnalazione: le Action `AssignTicketAction`, `ChangeTicketPriorityAction`, `DeleteTicketAction`,
`RecordTicketCommunicationAction`, `SetTicketSubscriptionAction`, `CanViewTicketMediaAction` e le risorse Filament la chiamano
tramite il `Gate`. Come arriva al `Gate`: [policy-registration.md](policy-registration.md).

## Input

- Un utente (`UserContract`; per `view` anche `null` = ospite) e, per le abilita' sul singolo ticket, il `Ticket`.
- Abilita': `viewAny`, `view`, `create`, `update`, `delete`, `deleteAny`, `assign`, `changeStatus`, `communicate`, `changePriority`.
- Ruoli PA: `BasePolicy::PA_ROLES` = `operator`, `supervisor`, `admin`. Permessi nominali: `ticket.<abilita'>`.

## Output

- `bool`. Il `Gate` traduce `false` in `AuthorizationException` (403) o in azione nascosta.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-POLICY-INV-1 | `viewAny`, `assign`, `changeStatus`, `communicate` e `changePriority` sono concesse a chi ha un ruolo PA (`operator`, `supervisor`, `admin`) o il permesso `ticket.<abilita'>`; a un cittadino senza permessi sono negate. Violata: un cittadino assegnerebbe o cambierebbe lo stato delle segnalazioni. | `TICKET-POLICY-INV-1` |
| TICKET-POLICY-INV-2 | I permessi nominali sono granulari: `ticket.assign` concede `assign` e non `changeStatus` ne' `changePriority`. | `TICKET-POLICY-INV-2` |
| TICKET-POLICY-INV-3 | `delete` e `deleteAny` sono concesse solo al ruolo `admin` o al permesso `ticket.delete`: ne' un operatore PA, ne' un supervisore, ne' il proprietario cancellano una segnalazione (la traccia pubblica non si cancella). | `TICKET-POLICY-INV-3` |
| TICKET-POLICY-INV-4 | Per `view` e `update` il proprietario e' deciso solo da `owner_id`: i campi di audit (`created_by`, `updated_by`) non danno mai accesso. Il proprietario puo' vedere e modificare, non cancellare. | `TICKET-POLICY-INV-4` |
| TICKET-POLICY-INV-5 | `view` per un ospite (`null`) e' vera solo se il ticket e' visibile sul front office pubblico (stato in `TicketStatusEnum::canViewByAll()`); per un utente autenticato e' vera anche per PA, permesso `ticket.view` e proprietario. | — (non dimostrato) |
| TICKET-POLICY-INV-6 | Solo il ruolo `super-admin` supera la Policy a priori (`BasePolicy::before()`); il ruolo `fixcity::admin`, che serve solo ad aprire il pannello e ce l'hanno tutti gli operatori, non concede nulla: un operatore con `fixcity::admin` non cancella. | `TICKET-POLICY-INV-6` |
| TICKET-POLICY-INV-7 | `create` e' concessa a ogni utente autenticato (identificativo non nullo) oltre che a chi ha `ticket.create`: il permesso, da solo, non restringe nulla. | — (non dimostrato) |
| TICKET-POLICY-INV-8 | Controllare un permesso nominale non ancora esistente lo crea (una `INSERT` in `permissions`, solo al primo controllo di quel nome) e non scrive piu' ai controlli successivi; l'esito dell'autorizzazione non cambia: falso senza permesso, vero dopo averlo concesso. | `TICKET-POLICY-INV-8` |

## Effetti collaterali

- Nessuna scrittura sul ticket. Unica eccezione `TICKET-POLICY-INV-8`: `hasPermissionToOrCreate` inserisce la riga del permesso
  mancante (una volta per nome; poi lo serve la cache del registro Spatie).
- Letture: ruoli e permessi dell'utente; `view` legge stato e `owner_id` del ticket.

## Errori

- Nessuna eccezione propria: ogni abilita' risponde `true` o `false`. Chi chiama tramite `Gate::authorize` riceve
  `AuthorizationException`.
- Limite noto: `view` si basa sullo stato (`canViewByAll`), non sulla posizione: un ticket in stato pubblico ma senza posizione e'
  "visibile" per la Policy mentre la mappa, l'elenco e il dettaglio pubblico lo escludono (`BuildPublicTicketsQueryAction` richiede `location`).
- Limite noto: `create` concesso a ogni autenticato rende ornamentale `ticket.create` (INV-7).

## Non fa

- Non decide l'accesso al pannello Filament ne' alle pagine CMS (`auth` nel JSON della pagina `tickets/create`).
- Non distingue per comune o per competenza territoriale: il comune non e' una colonna del ticket.
- Non autorizza i file allegati: per quelli c'e' `CanViewTicketMediaAction`, che si appoggia a `view` e `viewAny`.
- Non registra se stessa nel `Gate` (autodiscovery, vedi `policy-registration.md`).
