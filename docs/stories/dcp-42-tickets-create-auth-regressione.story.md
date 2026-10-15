---
id: dcp-42
title: "tickets/create: autenticazione prima del wizard"
type: story
epic: dc-parity-v2
status: ready-for-dev
priority: P1
owner: module:Fixcity + module:Cms
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [auth, cms-json, tickets-create, access-control]
qmd: "dcp-42 tickets create middleware auth guest redirect locale intended"
related: [dcp-35, dcp-41]
---

# DCP-42 — Autenticazione prima di tickets/create

## Story

Come cittadino autenticato, voglio aprire il wizard di creazione della segnalazione
solo dopo il login, così i dati personali e la persistenza del ticket hanno un
confine di sicurezza coerente e l'ospite non può compilare un form che il submit
rifiuterebbe comunque.

## Decisione

La pagina CMS tickets.create usa "middleware": ["auth"] [corretto 14:00 dalla sessione tema: con "web" la pagina risponde 500, vedi Verifica in fondo].
L'ospite viene reindirizzato al login localizzato con url.intended conservato.
La lista pubblica e il tracking restano accessibili senza autenticazione. La decisione
supera la precedente story guest-access perché l'azione di salvataggio richiede già
un utente autenticato e il form raccoglie dati personali.

## Acceptance Criteria

### AC1 — Configurazione CMS canonica

Dato il JSON del tenant Fixcity, allora tickets.create.json contiene esattamente ["auth"] (NON "web": 500; NON "guest": blocca gli autenticati) [corretto 14:00],
non guest, e la sua access_policy dichiara guest_access: false. Non vengono creati
slug o JSON alternativi.

### AC2 — Guest redirect localizzato

Dato un guest su /it/tickets/create o /en/tickets/create, allora la risposta è un
redirect al rispettivo login locale e url.intended conserva il percorso originale.
Le richieste JSON guest ricevono 401.

### AC3 — Accesso autenticato

Dato un utente autenticato IT/EN, allora la pagina risponde 200 e il widget Filament
completo viene renderizzato, senza cambiare wizard, schema, mappa o layout.

### AC4 — Confini delle pagine pubbliche

Dato un guest, allora /it/tickets e il tracking pubblico continuano a rispondere
secondo il loro contratto; solo la pagina di creazione è protetta.

### AC5 — Parità e misure

Le misure visual/DOM dei passi del wizard vengono ripetute da autenticato alle
viewport 375×900, 768×1000 e 1440×1200; nessuna percentuale precedente ottenuta
da guest viene presentata come evidenza del flusso autenticato.

### AC6 — Sicurezza e regressioni

La configurazione non espone dati personali a guest, il redirect non perde la locale
né l'URL intended, e non compaiono eccezioni Livewire/Filament dopo il login.

## Tasks / Subtasks

- [ ] T1. Allineare il JSON locale canonico e le copie di distribuzione senza introdurre un secondo slug. (AC: #1)
- [x] T2. Verificare Page::getMiddlewareBySlug() e PageSlugMiddleware con gli alias Laravel auth/web. (AC: #1, #2) [fatto 14:00: `web` NON e' un alias risolvibile da PageSlugMiddleware: `Target class [web] does not exist`; solo `auth`]
- [ ] T3. Coprire redirect IT/EN, intended URL, 401 JSON e accesso autenticato. (AC: #2, #3, #4, #6)
- [ ] T4. Aggiornare il contratto documentale e archiviare la story guest-access contraddittoria. (AC: #1, #4)
- [ ] T5. Ripetere screenshot, DOM snapshot, console check e WCAG del wizard dopo autenticazione. (AC: #5, #6)
- [ ] T6. Aggiornare il registro BMAD/Second Brain con la decisione e l'eventuale scarto tra copie. (AC: #1, #5)

## Dev Notes

- Il middleware di pagina è letto dal JSON tramite Page::getMiddlewareBySlug() e
  applicato da Modules\\Cms\\Http\\Middleware\\PageSlugMiddleware. [Source: laravel/Modules/Cms/app/Models/Page.php; laravel/Modules/Cms/app/Http/Middleware/PageSlugMiddleware.php]
- Il test esistente definisce già il contratto atteso: redirect localizzato per guest,
  intended URL e 401 su JSON. [Source: laravel/Modules/Fixcity/tests/Feature/Pages/TicketGuestLocaleRedirectTest.php]
- Il wizard Filament resta invariato e deve essere verificato da autenticato. [Source: laravel/Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php]
- La configurazione runtime del tenant locale è la fonte primaria; le copie module/eu
  devono mantenere lo stesso contratto quando vengono pubblicate. [Source: laravel/config/local/fixcity/database/content/pages/tickets.create.json]
- La parità visuale va rivalutata da autenticato perché la sezione Autore e il riepilogo
  possono cambiare geometria. [Source: bmad-output/decision-log.md; DCP-33/DCP-34]

## Testing strategy

1. Unit: validare che il JSON canonico abbia auth, non guest, e policy coerente.
2. Feature: eseguire TicketGuestLocaleRedirectTest, TicketCreateVoltRootTest e i test
   pagina IT/EN con utente autenticato.
3. Browser: login preparatorio, poi screenshot dei tre passi alle tre viewport;
   verificare console, intended redirect, tab order e assenza overflow.
4. Security: verificare che HTML, Livewire payload e redirect guest non contengano
   dati del ticket o dati personali.

## Dependency Maps

### Blocked by

- DCP-41 — risoluzione Folio/CMS del JSON canonico.
- DCP-32/DCP-33/DCP-34 — wizard e parity dei suoi passi.

### Blocks

- DCP-35 — submit/conferma coerenti con utente autenticato.
- Epic 2.1 — vertical slice cittadino.

## Owned File / Module Scope

- laravel/config/local/fixcity/database/content/pages/tickets.create.json
- laravel/config/eu/fixcity/database/content/pages/tickets.create.json
- laravel/Modules/Fixcity/config/database/content/pages/tickets.create.json
- laravel/Modules/Fixcity/tests/Feature/Pages/TicketGuestLocaleRedirectTest.php
- laravel/Modules/Fixcity/tests/Feature/Pages/TicketCreatePageMetadataTest.php
- laravel/Modules/Fixcity/docs/stories/dcp-42-tickets-create-auth-regressione.story.md
- laravel/Themes/Sixteen/docs/bmad/stories/fil-tickets-guest-access-why.story.md

Shared/contended: i18n/Folio middleware registration; non modificare il wizard o
la mappa in questa story.

## Learnings from Previous Stories

- Il middleware dichiarativo nel JSON è il confine corretto: non duplicare auth in
  una pagina Blade o in un controller. (STORY-357/DCP-41)
- guest non protegge una pagina: consente l'accesso anonimo e reindirizza gli utenti
  già autenticati; usarlo qui lasciava il form visibile all'ospite.
- Le misure visuali devono usare lo stesso stato di autenticazione dell'utente reale.

## Dev Agent Record

_(empty — da completare dall'external dev tool)_

## Verifica della sessione tema (2026-10-13 14:00, misurata, non dichiarata)
- Messaggio dell'utente: «Un ospite oggi puo' aprire /it/tickets/create, infatti dentro tickets.create.json possiamo mettere "middleware"»: decisione = autenticazione dichiarata nel JSON della pagina.
- Perche' un ospite entrava: la pagina del modulo `Modules/Fixcity/resources/views/pages/tickets/create.blade.php` ha `middleware('auth')`, ma Folio serve `/it/tickets/create` con la pagina dinamica del tema `pages/[container0]/[slug0]/index.blade.php` (che oscura quella del modulo), la cui unica protezione e' `PageSlugMiddleware`, che legge `middleware` dal JSON. Il JSON non l'aveva piu' dal commit `e040344c0` (2026-09-27).
- Il 13:57 il tenant JSON e' stato scritto con `"middleware": ["web","auth"]`: **500 per tutti** (`laravel.log`: `Target class [web] does not exist`, `PageSlugMiddleware::executeMiddlewareChain` risolve ogni voce come alias/classe). In piu' aveva `layout: test` (ripristinato a `design-comuni`) e un `access_policy` con `guest_access: true` in contraddizione con `auth`.
- Stato ora (3 copie identiche, md5 uguale): `"middleware": ["auth"]`, `"layout": "design-comuni"`, `access_policy` coerente (`guest_access: false`). Guest: `/it/tickets/create` e `/it/tickets/create?step=...` -> 302 a `/it/auth/login`; `/en/tickets/create` -> 302 a `/en/auth/login`; `/it`, `/it/tickets`, `/it/tickets/confirmation` -> 200.
- Test: `TicketGuestLocaleRedirectTest` (3 casi, dati rossi 3/3 da DCP-42) ora verde; `TicketCreateVoltRootTest`, `TicketCreatePageJsonGuardTest`, `CreateTicketWizardWidgetViewTest` verdi (13 passati, 46 asserzioni); nuovo `tests/Unit/TicketCreatePageMiddlewareGuardTest.php` (4 casi): ogni copia ha esattamente `["auth"]` e le tre copie sono identiche; PHPStan sul nuovo file a zero. Comando: `APP_ENV=testing DB_CONNECTION=sqlite APP_CONFIG_CACHE=/tmp/<inesistente> ./vendor/bin/pest <file>`.
- Non verificato: accesso autenticato IT/EN (nessuna credenziale di sviluppo nota: non l'ho indovinata), conservazione dell'URL `intended` dopo il login in browser (coperta solo dal test), 401 per le richieste JSON (coperto dal test).
- Conseguenza per le misure (AC5): i gate di DCP-33/34/35 sono tutti da ospite e ora si fermano al login. Serve un account di prova per `--prepare` (login) o l'autorizzazione a crearne uno nel database di sviluppo: richiesta all'utente.


## Decisione ed esito (2026-10-13 14:05 UTC)
Decisione dell'utente: mettere `"middleware"` nel JSON. Adottata la proposta 1 (`["auth"]`). Decisione e ragioni: `Themes/Sixteen/docs/decision-log/D17-tickets-create-richiede-auth.md` (tre livelli di codice che rifiutano il ticket anonimo).
Esito, con output: ospite -> 302 `/it/auth/login` (it) e `/en/auth/login` (en); `TicketGuestLocaleRedirectTest` + `TicketCreateVoltRootTest` + `TicketCreatePageJsonGuardTest`: 6 passati, 28 asserzioni, DB di sviluppo invariato (7 utenti, 2 ticket).
Incidente nello stesso lavoro: un altro agente ha scritto `["web","auth"]` (13:56:58 UTC) e la pagina e' andata in 500 per tutti (`Target class [web] does not exist`, `PageSlugMiddleware.php:154`); corretto in `["auth"]`. Lo stesso agente aveva aggiunto un blocco `access_policy.guest_access: true` in contraddizione con la decisione; non c'e' piu' nel file alle 13:58 (nessun codice lo leggeva).
Aperto: allineare le copie `Modules/Fixcity/config/...` ed `eu` (senza `middleware`); sessione autenticata per i gate (DCP-33/35, `parity-wizard-steps.sh` rifiuta di misurare la pagina di login); `layout` del JSON passato da `test` a `design-comuni` da un altro agente tra le 13:57 e le 13:58 (lo stato di DCP-32/33 decide).

Correzione 14:10 UTC (lead): il punto "Aperto: allineare le copie" non e' piu' vero, le tre copie sono identiche con `["auth"]` (`cmp`). Per `page-json-for-url.sh`, lo slug di una pagina protetta e' ora dedotto e dichiarato (DCP-41).

## Audit documentale 2026-10-13

- Le tre copie sono semanticamente equivalenti e hanno `middleware: ["auth"]`;
  l'equivalenza JSON non richiede hash byte-per-byte, perché la formattazione può
  differire tra tenant e modulo.
- `TicketGuestLocaleRedirectTest` e `TicketCreatePageMetadataTest` risultano
  verdi; il gate browser autenticato delle tre viewport resta aperto e non viene
  dichiarato completato senza screenshot e DOM evidence.
- Il rendering raw `fixcity::` non proveniva dal middleware: era causato da
  blocchi letterali `fixcity::...` dentro le strutture di
  `lang/it/ticket_form.php` e `lang/it/ticket_wizard.php`. Le strutture sono
  state normalizzate e il contratto inglese della checkbox privacy è stato
  aggiunto in `lang/en/ticket_form.php`.
