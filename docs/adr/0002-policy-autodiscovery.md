---
title: "Policy registrate tramite autodiscovery"
type: decision
status: accepted
created: 2026-11-23
updated: 2026-11-26
module: Fixcity
decision-makers: [team Laraxot]
---

# Policy registrate tramite autodiscovery

## Contesto

`TicketPolicy` (`Modules/Fixcity/app/Policies/TicketPolicy.php`) segue la convenzione Laravel per l'associazione automatica tra modello e Policy (`Models\Ticket` e `Policies\TicketPolicy`
dello stesso modulo). Una chiamata manuale a `Gate::policy(Ticket::class, TicketPolicy::class)` nel provider ripeterebbe la stessa responsabilita', appesantirebbe il bootstrap e farebbe
sembrare necessaria una registrazione che il framework gia' garantisce. Il pacchetto ha una sola fonte di verita' per l'associazione modello-Policy?

## Driver della decisione

- Una sola fonte di verita' per l'associazione modello-Policy.
- Provider focalizzati sul bootstrap realmente specifico del modulo.
- Compatibilita' con progetti che caricano Fixcity tramite autodiscovery.
- Test piu' semplici e minore rischio di registrazioni divergenti.

## Alternative scartate

- Registrare ogni Policy manualmente in ogni service provider.
- Spostare la registrazione in Cms, che non possiede il dominio Ticket.
- Introdurre un registry custom duplicato rispetto al framework.

## Decisione

Nessuna registrazione manuale in `FixcityServiceProvider`: `TicketPolicy` resta nella posizione e nella convenzione previste dall'autodiscovery. `FixcityServiceProvider::boot()` non chiama piu'
`Gate::policy(Ticket::class, TicketPolicy::class)` (codice: `laravel/Modules/Fixcity/app/Providers/FixcityServiceProvider.php`).

## Conseguenze

- Buono: il provider non importa piu' `Gate` ne' `TicketPolicy` per questa responsabilita'; le regole restano nel modulo e le chiamate applicative risolvono la Policy tramite il `Gate`.
- Cattivo: se la convenzione di naming si rompe (un rinomino di `Ticket` o di `TicketPolicy`, uno spostamento di cartella) il `Gate` non trova la Policy e **nega per default**: i sintomi sono azioni
  nascoste e risposte 403, non un errore di bootstrap.
- Se in futuro una Policy non potra' seguire l'autodiscovery, quella eccezione va documentata in un ADR specifico con test di bootstrap.

## Conferma

Contratto operativo: [policy-registration.md](../contracts/policy-registration.md), con la regola di autorizzazione in [ticket-policy.md](../contracts/ticket-policy.md).

Da `laravel/`:

```bash
rg -n "Gate::policy|Gate::define" Modules/Fixcity/app
vendor/bin/pest Modules/Fixcity/tests --group=TICKET-POLICY-REG-INV-2
```

Il primo comando non deve trovare nulla (verificato: nessuna corrispondenza). Il secondo esegue `it('shows soft-delete to an administrator')` in
`Modules/Fixcity/tests/Feature/Filament/TicketDeletionAuthorizationTest.php`: l'azione `delete` e' visibile solo se il `Gate` risolve `TicketPolicy` (senza Policy il `Gate` nega).
`Modules/Fixcity/tests/Unit/Policies/TicketPolicyTest.php` istanzia `TicketPolicy` direttamente e non prova l'associazione: prova le regole, non l'autodiscovery.
