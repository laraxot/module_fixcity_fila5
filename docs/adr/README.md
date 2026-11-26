---
title: "Architecture Decision Records"
type: index
tags: [adr, decisioni]
created: 2026-11-18
updated: 2026-11-26
module: Fixcity
---

# Architecture Decision Records

Il **perche'** delle scelte di questo pacchetto: una decisione per file, con le alternative scartate. Il nome del file e' `NNNN-argomento.md` (numero di decisione, niente date: la data sta nella frontmatter).

## Formato (MADR)

Frontmatter: `title`, `type: decision`, `status` (`accepted`, `proposed`, `rejected`, `deprecated`, `superseded`; se `superseded` anche `superseded_by`), `created`, `updated`, `module`, `decision-makers`. Sezioni `##` in quest'ordine:

1. **Contesto**: fatti verificati e vincoli, chiusi dalla domanda a cui la decisione risponde.
2. **Driver della decisione**: i criteri con cui si sceglie.
3. **Alternative scartate**: ogni alternativa seria, con il motivo.
4. **Decisione**: cosa si fa e dove sta nel codice.
5. **Conseguenze**: buone e cattive, debito che resta, cosa misurare.
6. **Conferma**: come si verifica che la decisione e' rispettata: un test esistente (percorso), un comando eseguibile da `laravel/` o una regola di review. Se non c'e' scrivere "Nessuna conferma automatica oggi; proposta: ...". Mai un test inventato.

Se il codice contraddice la decisione si aggiunge `## Stato attuale` con la prova (file e riga) e non si riscrive la storia. Chi cambia una decisione scrive un nuovo ADR e segna questo `superseded` con `superseded_by`.

## Decisioni

- [0001-fixcity-public-logo-path](0001-fixcity-public-logo-path.md): il logo si dichiara come `fixcity::svg/logo.svg`, l'unico file che esiste in `public/svg`.
- [0002-policy-autodiscovery](0002-policy-autodiscovery.md): `TicketPolicy` si associa al modello con l'autodiscovery, senza `Gate::policy()` nel provider.
- [0003-public-ticket-detail-route-ownership](0003-public-ticket-detail-route-ownership.md): il dettaglio pubblico `/tickets/{id}` e' un contenitore CMS, i dati li governa `BuildPublicTicketDetailAction`.
- [0004-route-trans-cache-deploy](0004-route-trans-cache-deploy.md): il deploy usa `route:trans:cache` (una cache per lingua) con ripiego su `route:clear`.
- [0005-ticket-lifecycle-is-action-owned](0005-ticket-lifecycle-is-action-owned.md): creazione, assegnazione, stato e comunicazioni di un ticket passano da Action e Policy.

Altri file della cartella: `0000-template.md` (modello, non e' una decisione: usa ancora `date:` e "Contesto e problema"), `index.md` (inventario generato).

## Stato di copertura

Misurato il 2026-11-26 leggendo i file; nessun test e' stato rilanciato in questa passata.

| ADR | Stato | Conferma | Stato attuale (codice che contraddice) |
|---|---|---|---|
| 0001 | accepted | manuale (`test -f`, `rg`); proposta: test Pest sui percorsi del logo | si: 20 righe `fixcity::images/` in 4 file di configurazione per lingua |
| 0002 | accepted | automatica: `--group=TICKET-POLICY-REG-INV-2` e `rg "Gate::policy|Gate::define"` | no |
| 0003 | accepted | automatica: 3 test citati (Fixcity e tema Sixteen) | parziale: un secondo costruttore di payload pubblico (`BuildTicketPublicDetailsPayloadAction`) |
| 0004 | accepted | manuale (`rg` su `deploy.sh`); nessun test legge `deploy.sh` | no |
| 0005 | accepted | automatica sui comportamenti (5 test), nessuna guardia contro scritture dirette di `status` | si: `TransitionTicketStatusAction` senza chiamanti, `TicketCreatedListener` con scrittura commentata |

Su 5 ADR: 3 con conferma automatica (0002, 0003, 0005 per i comportamenti), 2 solo manuale (0001, 0004). Tutte e 5 hanno le sei sezioni nell'ordine; 3 hanno anche `## Stato attuale` (0001, 0003, 0005).

## Backlog contract

Contratti collegati alle decisioni: [policy-registration](../contracts/policy-registration.md) e [ticket-policy](../contracts/ticket-policy.md) (0002), [build-public-ticket-detail-action](../contracts/build-public-ticket-detail-action.md) (0003), [change-status](../contracts/change-status.md), [assign-ticket-action](../contracts/assign-ticket-action.md), [create-ticket-action](../contracts/create-ticket-action.md) (0005). Le Action del ciclo di vita ancora senza contratto (Approve, Reject, Close, ConfirmTicketResolution, RecordTicketActivity) sono nel backlog di [../contracts/README.md](../contracts/README.md).

## Backlog test

- 0001: un test Pest in `Modules/Fixcity/tests/Unit` che legga ogni `config/**/metatag.php` del modulo e del tenant e verifichi che ogni valore `fixcity::...logo` punti a un file esistente in `Modules/Fixcity/public` (fallirebbe oggi sulle 4 copie per lingua).
- 0004: un test Arch o di file che verifichi la riga `route:trans:cache` in `deploy.sh`.
- 0005: un test Arch che vieti `setStatus` fuori da `Modules/Fixcity/app/Actions`.
- 0003: un test che confronti la regola di visibilita' di `BuildTicketPublicDetailsPayloadAction` con quella di `BuildPublicTicketDetailAction`.

## Decisioni precedenti (formato libero, non ancora ADR)

- [wizard-submission-states](../wiki/decisions/wizard-submission-states.md)
- [bmad-plan-services-to-actions](../decisions/bmad-plan-services-to-actions.md)
- [no-services-final-status](../decisions/no-services-final-status.md)
- [themed-access-errors-through-http-exception-handler](../decisions/themed-access-errors-through-http-exception-handler.md)
- [ticket-hours-user-boundary](../decisions/ticket-hours-user-boundary.md)
