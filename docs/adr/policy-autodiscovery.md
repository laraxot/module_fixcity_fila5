---
title: "ADR — Policy registrate tramite autodiscovery"
type: decision
module: Fixcity
status: accepted
updated: 2026-11-23
---

# Policy registrate tramite autodiscovery

## Contesto

`TicketPolicy` segue la convenzione Laravel per l'associazione automatica tra
modello e Policy. Una chiamata manuale a `Gate::policy(Ticket::class,
TicketPolicy::class)` nel provider ripete la stessa responsabilità, appesantisce
il bootstrap e fa sembrare necessaria una registrazione che il framework già
garantisce.

## Driver della decisione

- una sola fonte di verità per l'associazione modello-Policy;
- provider focalizzati sul bootstrap realmente specifico del modulo;
- compatibilità con progetti che caricano Fixcity tramite autodiscovery;
- test più semplici e minore rischio di registrazioni divergenti.

## Decisione

Rimuovere la registrazione manuale da `FixcityServiceProvider` e mantenere
`TicketPolicy` nella posizione e nella convenzione Laravel previste
dall'autodiscovery.

## Alternative scartate

- registrare ogni Policy manualmente in ogni service provider;
- spostare la registrazione in Cms, che non possiede il dominio Ticket;
- introdurre un registry custom duplicato rispetto al framework.

## Conseguenze

Il provider non importa più `Gate` né `TicketPolicy` per questa responsabilità.
Le regole restano nel modulo Fixcity e le chiamate applicative continuano a
risolvere la Policy tramite il Gate Laravel. Se in futuro una Policy non potrà
seguire l'autodiscovery, quella eccezione dovrà essere documentata in un ADR
specifico con test di bootstrap.

## Conferma

Il contratto operativo è
[policy-registration.md](../contracts/policy-registration.md). La suite
`TicketPolicyTest` e PHPStan verificano il comportamento senza una registrazione
manuale nel provider.

## Correzione applicata

`FixcityServiceProvider::boot()` non chiama più `Gate::policy(Ticket::class, TicketPolicy::class)`; l'associazione modello-Policy resta affidata all'autodiscovery. Codice: `laravel/Modules/Fixcity/app/Providers/FixcityServiceProvider.php`.
