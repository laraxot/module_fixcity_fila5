---
title: "FixCity public tracking rate-limit experience"
type: story
status: in-progress
module: Fixcity
actor: Citizen (Anonymous)
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, tracking, accessibility, localization]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../workflows/actor-visitor.md
  - ../gap-analysis.md
  - STORY-037-public-ticket-tracking-page.md
---

# Problema

Il tracking limita già i guest a 10 richieste al minuto, ma il 429 veniva reso
con la pagina Laravel generica in inglese. Il cittadino non riceveva una ragione
chiara né un percorso per riprendere la ricerca.

# Criteri di accettazione

- [x] Il limite guest resta 10 richieste al minuto.
- [x] Il 429 del solo tracking usa una vista FixCity accessibile e localizzata IT/EN.
- [x] Il messaggio comunica l'attesa e offre un link che riapre la ricerca senza
      riproporre il codice nella query.
- [x] La risposta conserva l'header `Retry-After`.
- [x] L'handler restituisce la vista personalizzata solo per `tickets.track`; per
      le altre route non tratta l'eccezione.
- [ ] Verifica browser responsive della schermata 429. Playwright/Chromium ora si avviano usando dipendenze isolate in `/tmp`; la risposta 429 visuale non è ancora stata esercitata.

# Verifiche

Pest mirato: 10 test / 89 asserzioni passano, inclusi 429 IT/EN, `Retry-After`,
rate limit, form tracking valido/errato e owner lookup. Suite FixCity: 382 test /
1.666 asserzioni passano. PHPStan `Modules` senza errori; `view:cache`, Pint
mirato e quality gate wiki passano. Il controllo browser di 429 resta da eseguire:
Chromium è disponibile dopo l'estrazione locale delle dipendenze sotto `/tmp`; non
è più corretto descrivere il blocco come browser non installato.
