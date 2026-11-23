---
title: "Contratto di registrazione delle Policy Fixcity"
type: framework-contract
contract: Modules\\Fixcity\\Policies\\TicketPolicy
owner: Fixcity
status: accepted
updated: 2026-11-23
---

# Registrazione automatica delle Policy

Le Policy del modulo sono scoperte e registrate dal meccanismo Laravel di
autodiscovery delle Policy. Il service provider del dominio non deve duplicare
questa registrazione con `Gate::policy()`.

## Promessa

- `TicketPolicy` resta nella directory `Modules/Fixcity/app/Policies`.
- Il modello `Ticket` e la Policy mantengono la convenzione di naming Laravel.
- Le chiamate a `Gate`, `authorize()` e ai Filament Resource usano la Policy
  risolta dal framework.
- Il provider Fixcity si occupa soltanto dell'infrastruttura realmente propria
  del modulo: osservatori, route API, configurazione e registrazioni esplicite
  non coperte dall'autodiscovery.

## Non fa

- Non aggiunge una registrazione manuale ridondante in
  `FixcityServiceProvider`.
- Non sposta la Policy in Cms o Xot.
- Non modifica le regole di autorizzazione della Policy.

## Verifica

```bash
cd laravel
vendor/bin/pest Modules/Fixcity/tests/Unit/Policies/TicketPolicyTest.php
vendor/bin/phpstan analyse Modules/Fixcity
```

## Correzione applicata

Nessuna registrazione manuale in `FixcityServiceProvider::boot()`: `TicketPolicy` è risolta dall'autodiscovery. Vedi `laravel/Modules/Fixcity/app/Providers/FixcityServiceProvider.php`.
