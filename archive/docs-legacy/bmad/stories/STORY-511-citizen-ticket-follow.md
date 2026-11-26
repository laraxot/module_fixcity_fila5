---
title: STORY-511 — Seguire una segnalazione
type: story
status: in_progress
module: Fixcity
created: 2026-09-26
updated: 2026-09-26
tags:
- bmad
- fixcity
- citizen
- subscription
- accessibility
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/572
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/573
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
related:
- ../gap-analysis.md
- ../../wiki/concepts/user-journey-map.md
- ../../wiki/concepts/ticket-subscribers-vs-comment-subscribers.md
qmd: STORY 511 citizen ticket follow FixCity BMAD story
---

# STORY-511 — Seguire una segnalazione

## Scopo

Un cittadino autenticato può aggiungere/rimuovere una segnalazione dalla propria
lista delle seguite solo se la policy consente di vedere il ticket.

## Criteri di accettazione

- [x] La tabella follower usa lo stesso identificativo UUID stringa del modello User via `foreignIdFor`.
- [x] Action idempotente per iscrizione/disiscrizione e serializzata sul ticket.
- [x] Accesso verificato con `TicketPolicy::view`; ticket privati di altri utenti negati.
- [x] Dettaglio FO offre CTA accessibile e login per i guest.
- [x] Test per idempotenza e autorizzazione aggiunti.
- [x] Activity observer invia notifiche in-app al proprietario e ai follower solo per transizioni pubbliche, dopo il commit; destinatari duplicati vengono deduplicati.
- [x] La notifica apre il tracking tramite codice capability, senza esporre un endpoint basato sull'ID sequenziale.
- [x] Test per destinatari e per esclusione degli eventi interni aggiunti.
- [x] L'operatore appena assegnato riceve una notifica database interna con link alla resource PA; nessun dettaglio privato è inviato al cittadino.
- [x] L'assegnazione e la relativa Activity sono atomiche e serializzate; l'evento conserva il nuovo assignee per il dispatch post-commit.
- [x] Test per la notifica al solo responsabile assegnato aggiunto.
- [x] Area personale espone l'elenco delle segnalazioni seguite, limitato all'utente autenticato e alle segnalazioni pubbliche o di cui è proprietario, con stato vuoto, conteggio e link al tracking.
- [x] Il link dell'elenco usa il codice-capability; ticket storici senza codice mostrano un messaggio esplicito e non generano URL vuoti.
- [x] Le notifiche di cambio stato aprono tracking per codice; il payload non espone l'ID sequenziale del ticket.
- [x] Tracking guest limitato a 10 richieste/minuto, coperto da test HTTP.
- [x] Le pagine "Le mie segnalazioni" e "Segnalazioni seguite" si collegano tra loro.
- [x] Pest di azioni, notifiche, pagine e API verificati sul database SQLite condiviso in transazione.
- [ ] Esecuzione migrazione/verifica schema su MariaDB test e staging (grant `fixcity_data_test` non disponibile; non modificato).
- [ ] UI browser responsive/autenticata: Playwright/Chromium sono disponibili, ma il flusso autenticato follower non è ancora stato verificato nel browser con account e ticket sintetici.
- [ ] Preferenze e notifiche email/push restano da completare.

## Note di migrazione

La migrazione evolutiva converte `user_id` da intero alla chiave UUID del modello
User preservando i valori storici. Eventuali valori numerici non associabili a un
utente attuale non vengono cancellati; richiedono audit dati separato.

## Verifiche eseguite

- PHPStan `analyse Modules`: 0 errori.
- Pint e `php artisan view:cache`: passati.
- Pest `SetTicketSubscriptionActionTest`, `TicketStatusNotificationTest`, `TicketAssignmentNotificationTest`, `BuildAuthenticatedUserFollowedTicketsQueryActionTest` e route tracking: eseguiti su SQLite condiviso; esiti nella [verifica runtime tracking](../tracking-links-capability-2026-09-27.md).
- `verify-llm-wiki.sh`: resta rosso per frontmatter/merge marker legacy esterni al modulo; nessun finding sullo story o sui file Fixcity di questa tranche.
