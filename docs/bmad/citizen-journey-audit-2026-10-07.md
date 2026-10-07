---
title: "FixCity — audit dei flussi cittadino"
type: bmad-journey-audit
status: static-audit-complete-runtime-pending
module: Fixcity
created: 2026-10-07
updated: 2026-10-07
tags: [bmad, journeys, citizen, auth, tickets, tracking, homepage, second-brain]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
related:
  - ./homepage-audit-2026-10-07.md
  - ./actor-journeys.md
  - ./actor-flows.md
  - ./stories/STORY-507-actor-flows-and-user-journeys.md
  - ../../../User/docs/bmad/citizen-auth-flow-contract-2026-10-07.md
  - ../../../Themes/Sixteen/docs/bmad/citizen-journey-presentation-contract-2026-10-07.md
---

# Audit dei flussi cittadino

## Scopo del prodotto

FixCity deve portare un cittadino anonimo dalla homepage alla segnalazione di un
problema urbano, rendere visibili le segnalazioni pubbliche e permettere al cittadino
registrato di ritrovare, seguire e valutare le proprie pratiche. Il valore non è il
form isolato: è la continuità tra identità, territorio, ticket, trasparenza e risposta.

## Mappa end-to-end

```text
Homepage guest
  -> registrazione o login
  -> accesso al flusso di segnalazione
  -> privacy, dati, posizione, riepilogo
  -> creazione ticket
  -> conferma e codice capability
  -> lista/mappa pubblica e dettaglio
  -> tracking, area personale, notifiche
  -> assegnazione PA, aggiornamenti, risoluzione, rating
```

## Matrice expected / observed

| Flusso | Scopo | Owner | Stato statico | Prova richiesta |
|---|---|---|---|---|
| Homepage | Orientare guest verso invio, elenco o tracking | Sixteen + Fixcity | Implementato, contratto aggiornato | HTTP/browser per locale e viewport |
| Registrazione | Creare identità cittadino con consenso e password valide | User + Gdpr | View/widget/test presenti | Submit reale, errori, mail/verifica |
| Login | Autenticare e conservare la destinazione richiesta | User + Sixteen | Widget, redirect e test presenti | Login UI con account verificato |
| Verifica email | Abilitare l’account e ripetere l’invio del link | User + Notify | Folio page e action Livewire presenti | Mail fake + link firmato |
| Elenco/mappa | Far consultare segnalazioni pubbliche senza esporre capability code | Fixcity + Geo + Sixteen | API, list, privacy test presenti | GeoJSON 200, popup, filtri, mobile |
| Creazione | Raccogliere privacy, dati, posizione e allegati | Fixcity + Geo + Media | Wizard Folio/Actions presente | Submit completo su DB di test |
| Conferma | Restituire esito, codice e prossima azione | Fixcity + Sixteen | Page e test unitari presenti | Ticket creato e link tracking valido |
| Tracking | Consultare stato pubblico con codice, con privacy per owner | Fixcity | Route, timeline e test presenti | codice valido/invalido e rate limit |
| Area personale | Vedere proprie pratiche e pratiche seguite | User + Fixcity | Pages, query Actions e test presenti | account isolati e stato vuoto |
| Notifiche | Informare su cambi di stato e preferenze | Notify + Fixcity | eventi/preferenze implementati | queue, mail, retry e opt-out |
| Rating | Raccogliere feedback dopo risoluzione | Rating + Fixcity | Contratti e story presenti | ticket risolto, una valutazione, doppio invio |
| PA | Prendere in carico, assegnare, lavorare e chiudere | Fixcity + Activity | Resource, policy, activity presenti | ruolo operatore e timeline audit |

## Evidenze statiche già trovate

- Folio mostra le route runtime con `php artisan folio:list`; `/it` usa
  `Themes/Sixteen/resources/views/pages/index.blade.php`.
- `/it/auth/register`, `/it/auth/login`, `/it/auth/verify` appartengono alle view
  Sixteen; i widget di autenticazione appartengono a User.
- `/it/tickets/create`, `/it/tickets/confirmation`, `/it/tickets/track` e il dettaglio
  appartengono al modulo Fixcity.
- `HomepageDesignContractTest` passa e protegge la view attiva da slug `tests`, CSS
  inline e CTA non localizzate.
- I test esistenti coprono redirect guest, privacy capability code, ticket list/API,
  pratiche personali, seguite e header auth. Sono evidenze di contratto, non collaudo
  completo di browser e database.

## Gap che impediscono di dire “il cittadino riesce”

1. Manca una prova unica che parta da homepage e concluda registrazione, login,
   creazione, conferma e tracking con lo stesso account.
2. La registrazione e la verifica email richiedono un ambiente mail/test configurato;
   i test di presenza del widget non dimostrano l’invio reale.
3. Le prove browser usano account/demo e alcuni test possono fare `skip` su server o
   database non disponibili. `exit 0` non è una prova positiva sufficiente.
4. Il percorso “crea segnalazione” dalla homepage deve essere verificato sia per guest
   che per account autenticato, inclusi redirect alla login e ritorno alla destinazione.
5. Notifiche, worker, SMTP, rating post-risoluzione e flusso PA richiedono una prova
   staging; non vanno dichiarati completati dal solo codice presente.
6. Le lingue supportate devono essere percorse almeno in `it`, `en`, `de`, `es`; ogni
   CTA e messaggio di errore deve restare tradotto.

## Acceptance criteria BMAD

- Un guest apre `/{locale}`, vede una CTA di invio, l’elenco pubblico e il tracking.
- Un guest che apre un’area protetta viene portato a `/{locale}/auth/login` e dopo il
  login torna alla destinazione richiesta.
- Un nuovo utente completa registrazione, consenso e verifica email senza chiavi grezze,
  errori silenziosi o perdita del locale.
- Un utente autenticato completa privacy, dati, posizione, riepilogo e invio; la conferma
  mostra un riferimento utilizzabile.
- Un guest può vedere solo dati pubblici; il capability code non appare nella lista,
  GeoJSON o dettaglio non autorizzato.
- Il proprietario vede la propria pratica, timeline pubblica e tracking; un altro utente
  non vede dati privati.
- Un operatore autorizzato può assegnare e aggiornare lo stato; l’attività è registrata.
- A ticket risolto il cittadino può inviare una sola valutazione; preferenze notifiche e
  consegna vengono rispettate.
- Tutti i percorsi passano a 320/390/768/1024/1440 px, tastiera, focus, contrasto e
  `prefers-reduced-motion`.

## Piano di verifica

```bash
cd laravel
php artisan folio:list
vendor/bin/pest Themes/Sixteen/tests/Unit/HomepageDesignContractTest.php
vendor/bin/pest Modules/Cms/tests/Feature/Frontoffice/HomepageRoutingTest.php \
  Modules/Cms/tests/Feature/Frontoffice/IndividualFolioRoutesTest.php \
  Modules/Fixcity/tests/Feature/Pages/TicketGuestLocaleRedirectTest.php \
  Modules/Fixcity/tests/Feature/Pages/TicketPagesTest.php \
  Modules/Fixcity/tests/Feature/Pages/FollowedTicketsPageTest.php
```

Il gate browser deve usare account creati nel test, `Mail::fake()`/mail sink, un database
isolato e report espliciti per ogni passaggio. Se l’infrastruttura non è disponibile, il
risultato deve essere `BLOCKED: environment`, non `PASS`.

## Decisione

La homepage è solo l’ingresso. Il prossimo incremento di qualità deve essere una prova
verticale cittadino completa, con ownership separata: User per identità, Fixcity per
ticket, Geo per posizione, Media per allegati, Notify per consegna, Rating per feedback,
Sixteen per presentazione e Cms per routing/contenuto.
