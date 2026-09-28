---
qmd: "actor journeys"
title: "FixCity — indice BMAD dei percorsi attore"
type: journey-index
status: active
created: 2026-09-26
updated: 2026-09-27
tags: [bmad, journeys, actors, auth, ux]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../wiki/concepts/user-journey-map.md
  - workflows/README.md
---

# Percorsi utente: fonte canonica

La matrice completa **atteso / presente nel codice / possibile oggi / fatto / mancante**
è la [mappa percorsi utente FixCity](../wiki/concepts/user-journey-map.md). È la sola
fonte analitica per evitare versioni discordanti tra BMAD e wiki.

Copre guest, cittadino non verificato e verificato, operatore PA, supervisore,
amministratore, sistema e la prima schermata attesa per ruolo. Le schede di esecuzione
sono [cittadino](workflows/actor-citizen.md), [operatore](workflows/actor-operator.md),
[supervisore](workflows/actor-supervisor.md), [amministratore](workflows/actor-admin.md)
e [sistema](workflows/actor-system.md).

## Ultime correzioni implementative

- La home Sixteen ora presenta la finalità del servizio e CTA localizzate per creare un account, accedere o iniziare una segnalazione.
- `/segnalazioni/create` inoltra al percorso canonico `tickets.create`, per cui la configurazione CMS applica l'accesso autenticato; il form simulato è stato rimosso.
- Il wizard passa da `CreateTicketAction`; l'elenco pratiche filtra per `owner_id`, non per il campo audit `created_by`.
- `/area-personale/seguite` interroga i ticket iscritti all'identificativo dell'utente autenticato tramite `BuildAuthenticatedUserFollowedTicketsQueryAction`; nasconde stati interni di terzi e offre tracking e stato vuoto localizzati.
- `/area-personale/impostazioni` espone il controllo delle email FixCity; l'opt-out non disattiva la casella notifiche in-app.
- L'assegnazione PA è atomica con l'Activity; il nuovo operatore riceve una notifica database post-commit e il proprietario cittadino non riceve il dettaglio di un evento interno.

## Verifica ospite — 2026-09-27

Smoke browser locale senza autenticazione (server `APP_ENV=testing`, DB SQLite):

| Percorso | Esito osservato | Nota |
|---|---|---|
| `/it/` | 200, titolo “Elenco segnalazioni” | È coerente con `config/local/fixcity/database/content/pages/home.json`: la homepage FixCity presenta mappa/lista live e CTA per inviare una segnalazione. Il DB non è stato modificato. |
| `/it/auth/login`, `/it/auth/register` | 200 con form | Corretto il catalogo IT login che produceva 500; dettaglio in `Modules/User/docs/bmad/guest-login-italian-translation.md`. |
| `/it/segnalazioni` | 200, lista/mappa live, titolo “Segnalazioni” | Responsive 320/768/1.440; CTA porta a `/it/tickets/create`, da guest al login localizzato. Un CTA, nessun overflow/errore JS; [audit UI/UX](ui-ux-runtime-audit-2026-09-26.md). |
| `/it/segnalazioni/create`, `/it/area-personale/pratiche`, `/it/area-personale/seguite` | Redirect al login, pagina finale 200 | Verificato il guard guest; creazione e area personale dopo login restano da provare con account. |
| `/it/tickets/track` | 200, form con etichette localizzate | Lookup positivo con capability code non provato nel browser. |

La suite Fixcity SQLite passa (350 test / 1.440 asserzioni) e PHPStan globale è a zero errori. Il test MySQL/staging, la verifica visuale browser autenticata e i flussi end-to-end su ambienti di deploy restano aperti; il DB MariaDB condiviso non è stato modificato.
