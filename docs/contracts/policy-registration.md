---
title: "Contratto di registrazione delle Policy Fixcity"
type: contract
action: "Modules\\Fixcity\\Policies\\TicketPolicy"
module: Fixcity
status: accepted
tags: [contract, policy, autodiscovery, provider]
created: 2026-11-23
updated: 2026-11-26
---

# Registrazione automatica delle Policy

Come `TicketPolicy` arriva al `Gate` (il perche' sta nell'ADR [policy-autodiscovery](../adr/0002-policy-autodiscovery.md)). Le regole
di autorizzazione della Policy hanno un contratto a parte: [ticket-policy.md](ticket-policy.md).

## Input

- Una chiamata a `Gate`, `authorize()`, `$user->can()` o a una risorsa Filament su un `Modules\Fixcity\Models\Ticket`.

## Output

- Il `Gate` risolve `Modules\Fixcity\Policies\TicketPolicy` con l'autodiscovery di Laravel (modello `Ticket` in `Models`,
  Policy `TicketPolicy` in `Policies` dello stesso modulo), senza registrazione esplicita.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-POLICY-REG-INV-1 | `FixcityServiceProvider` (e nessun altro file di `app/`) non registra `TicketPolicy` a mano: nessun `Gate::policy()` ne' `Gate::define()` in `Modules/Fixcity/app`. Violata: due fonti di verita' per l'associazione modello-Policy. Controllo manuale: il comando `rg` sotto la tabella non deve trovare nulla. | — (non dimostrato) |
| TICKET-POLICY-REG-INV-2 | `TicketPolicy` resta in `Modules/Fixcity/app/Policies` e il suo nome segue la convenzione `Ticket` -> `TicketPolicy`: le chiamate al `Gate` su un `Ticket` raggiungono le sue regole (un amministratore vede l'azione `delete`, un operatore no). | `TICKET-POLICY-REG-INV-2` |

Verifica, da `laravel/` (la terza riga e' il controllo manuale di `TICKET-POLICY-REG-INV-1`, che non trova nulla):

```bash
vendor/bin/pest Modules/Fixcity/tests --group=TICKET-POLICY-REG-INV-2
vendor/bin/phpstan analyse Modules/Fixcity
rg -n "Gate::policy|Gate::define" Modules/Fixcity/app
```

## Effetti collaterali

- Nessuno a runtime: l'associazione la calcola il framework alla prima autorizzazione. Il provider si occupa solo
  dell'infrastruttura propria del modulo (osservatori, route API, configurazione, middleware nell'allowlist CMS).

## Errori

- Nessuna eccezione propria. Se la convenzione di naming si rompe il `Gate` non trova la Policy e **nega per default**: i sintomi
  sono azioni nascoste e risposte 403, non un errore di bootstrap. Se una Policy non puo' seguire l'autodiscovery serve un ADR
  con test di bootstrap (come dice l'ADR).

## Non fa

- Non aggiunge una registrazione manuale ridondante in `FixcityServiceProvider`.
- Non sposta la Policy in Cms o Xot.
- Non modifica le regole di autorizzazione della Policy (vedi `ticket-policy.md`).
