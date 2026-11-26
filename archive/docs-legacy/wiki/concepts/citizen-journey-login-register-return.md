---
title: "Viaggio del cittadino: accesso e ritorno alla pagina richiesta"
type: concept
tags: [auth, journey, login, registrazione, url-intended, tickets]
created: 2026-10-13
updated: 2026-10-13
qmd: "viaggio cittadino login registrazione url.intended redirectIntended Segnala disservizio dashboard 404 area personale"
module: Fixcity
story: Modules/Fixcity/docs/stories/dcp-124-viaggio-cittadino-login-registrazione-ritorno.story.md
related:
  - ../../stories/dcp-124-viaggio-cittadino-login-registrazione-ritorno.story.md
  - ../../../../../Themes/Sixteen/docs/decision-log/D18-login-e-registrazione-tornano-alla-pagina-richiesta.md
  - ticket-crea-url-routing-chain.md
status: draft
---

# Viaggio del cittadino: accesso e ritorno alla pagina richiesta

**Scopo.** L'elenco pubblico `/{lingua}/tickets` e' in sola lettura; la sola azione che produce valore e' segnalare, e segnalare richiede un account (D17). Il percorso critico e' quindi: ospite -> «Segnala disservizio» -> accesso o registrazione -> modulo di segnalazione.

**Flusso corretto**
1. L'ospite apre `/{lingua}/tickets/create`: il middleware `auth` del JSON risponde 302 al login e Laravel salva l'URL richiesto in `url.intended` (`redirectGuestsTo`, `bootstrap/app.php`).
2. `LoginWidget` e `HandleSuccessfulRegistrationAction` chiamano `redirectIntended(default: ...)`: pagina richiesta; senza, `route('dashboard')` se esiste, altrimenti `/{lingua}`.
3. Il cittadino arriva al modulo senza rifare il percorso.

**Trappole**
- `route('dashboard')` nel front office e' un 404 (pagina «Genesis» del modulo User oscurata dal catch-all `[container0]` del tema).
- I test di Cms e Gdpr non girano col runner di root; i test del viaggio stanno in `Modules/Fixcity/tests/Feature/Auth/`.
- L'accesso e' solo email e password: SPID/CIE (CAD art. 64) non c'e', quindi il copy del riferimento «autenticati con SPID o CIE» non si usa.

**Prova.** `AuthReturnsToRequiredPageTest` (3 test), mutation check, Playwright su fixture isolata (`scratch-auth-fixture.sh`).
