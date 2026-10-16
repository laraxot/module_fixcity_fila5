---
id: DCP-250
title: Completamento del progetto misurato dal gate go-live
type: epic
status: in_progress
created: 2026-10-15
updated: 2026-10-15
related: [DCP-240, DCP-251, DCP-252, DCP-253, DCP-254]
reserved_numbers: DCP-250..259
---

# DCP-250: completamento del progetto misurato dal gate

Richiesta dell'utente (2026-10-15): «completa il progetto utilizzando bmad + second brain + ponytail + ecc + filament blueprint». "Completo" qui e' definito da `bmad-output/go-live-gap-register.md` (stato **NO-GO**) e dal comando `python3 -I bashscripts/tools/release/go-live-gate.py`. Regola del registro: un gap si chiude solo con evidenza; non si inventano titolare, DPO, basi giuridiche, retention, SMTP, credenziali, decisioni di prodotto.

## Misura di partenza (2026-10-15 ~14:12 UTC)

Gate: **NON pubblicabile, 12 bloccanti falliti, 3 LG non soddisfatti, 3 avvisi, 15 superati su 33**. PHPStan `analyse Modules`: exit 0, 0 errori (non prova i test, vedi DCP-240).

| Controllo | Responsabile | Chiudibile da sviluppo ora? |
|---|---|---|
| ENV-01..03, ENV-06 (APP_ENV, DEBUG, DEBUGBAR, SESSION_SECURE_COOKIE) | sviluppo / fornitore | No: valori del `.env` di destinazione; misurabili con `--env-file laravel/.env.production.example` |
| ENV-04, ENV-05, NET-01 (dominio, https, mittente) | titolare / fornitore | No |
| LEG-01 guardie di pubblicazione (12 falliti) | ente (testi) | No: rosse per costruzione finche mancano i testi |
| LEG-02, LEG-03, LEG-04, LG-02 DPO, LG-08 licenza | ente (dati, testi, decisione) | No |
| REPO-02 nomi `.md` (519) | sviluppo | In parte: 466 sono in `docs/archive` e `docs/consolidated` di Notify, User, Xot, 23 in skill BMAD di terzi (nomi citati dai file stessi), 1 in `.ecc/` ignorato da git. Perimetro del gate: DEC-51 |
| LG-06 data di ultimo aggiornamento, LG-07 valutazione pagina | sviluppo (DCP-167, DCP-169) | Si, ma serve build del tema Sixteen: DEC-40 |
| Qualita dei test (non nel gate, necessaria a G-05) | sviluppo | Si: 8 rossi e 4 morti in DCP-240 |
| Registro richieste interessato (GDPR 15-22), blueprint in bozza | sviluppo + DPO | **Non senza risposta dell'utente**: richiede nuove tabelle |

## Come si usano gli strumenti richiesti

- **BMAD:** questa epic + una story per area nel modulo/tema owner; chiusura solo con output reale.
- **Second brain:** memoria + wiki aggiornati a fine task; decisioni nel registro DCP-200.
- **Ponytail:** diff piu corto che risolve la causa radice; niente astrazioni, niente file nuovi non richiesti; ogni "salto" dichiarato con soglia.
- **ECC** (plugin `ecc@ecc` 2.2.3 in `~/.claude/plugins/cache/ecc/ecc/2.2.3/`, copia project-local in `.cursor/`, ignorata da git): si usano `agents/code-reviewer.md` come checklist di revisione del proprio diff e `skills/laravel-verification` nelle sole fasi compatibili (`composer validate`, test via `pest-scratch-db.sh`, `composer audit`). Non si applicano `skills/laravel-patterns` (prescrive Services/Repository) ne `rules/php/hooks.md` (Pint): contro le regole del repo. Vedi `docs/wiki/concepts/ecc-everything-claude-code-valutazione.md`.
- **Filament Blueprint:** `laravel/.claude/skills/planning-filament` e `reviewing-filament-plans`. I blueprint esistenti: `Modules/Gdpr/docs/blueprints/registro-richieste-interessato.md` (bozza), `Modules/Fixcity/docs/blueprints/riepilogo-anteprima-foto.md`.

## Flussi di lavoro e proprietari

| Story | Area | Scope (file disgiunti) | Stato |
|---|---|---|---|
| DCP-251 | Tenant: test rossi e skipped | `Modules/Tenant/tests/**` | assegnato (sessione 5cf9209d) |
| DCP-252 | Test morti Blog e User | `Modules/Blog/tests/**`, `Modules/User/tests/Feature/Api/UserApiTest.php` | assegnato (sessione 5cf9209d) |
| DCP-253 | Front office: misura WCAG/pagine/LG-06/LG-07 (sola lettura) | nessun file di codice | assegnato (sessione 5cf9209d) |
| DCP-254 | Revisione del blueprint Gdpr con le skill Filament | `Modules/Gdpr/docs/blueprints/**`, `Modules/Gdpr/docs/stories/**` | assegnato (sessione 5cf9209d) |
| (da assegnare) | Notify test (3 rossi), job Fixcity, Xot `ModuleTest` | lockati da altri alle 13:35-13:37 | lock da verificare: soglia stantio 60 minuti |

## Regole per chi lavora

Uguali a DCP-240: niente `@phpstan-ignore`, niente baseline, niente cancellazione di test, niente Pint su cartelle di modulo, nessun comando git che scrive (in 7 repo di modulo c'e un merge in corso, DEC-50), test solo con `pest-scratch-db.sh` da `laravel/` e variabili `DB_DATABASE_LIMESURVEY= APP_CONFIG_CACHE=/nonexistent/config.php`, lock con `bashscripts/lock/`, attendere 3 minuti dall'ultimo mtime altrui. Nessuna nuova tabella o colonna senza risposta dell'utente.
