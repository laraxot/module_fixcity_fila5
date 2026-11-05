---
title: "Fixcity — Backlog prioritizzato e roadmap di completamento"
type: plan
tags: [fixcity, audit, backlog, roadmap, production-readiness]
created: 2026-11-05
updated: 2026-11-05
status: active
related:
  - ./README.md
  - ../stories/production-hardening-p0.story.md
---

# Backlog e roadmap

Il percorso critico è: **produzione stabile e sicura → CI che esegue davvero i test → workflow dati affidabile → requisiti PA**.
Nessuna funzionalità nuova ha senso finché il back office in produzione non funziona e la CI non può fallire.

Stato: **Risolto** = codice corretto e committato su `dev`, in attesa di test eseguiti; **Persona** = serve un'azione umana
(server, credenziali, decisione); **Aperto** = da fare. Gli ID rimandano ai rapporti di area.

## P0 — Blocchi

| ID | Problema | Stato | Criterio di accettazione |
| --- | --- | --- | --- |
| PROD-01 | Tutti i pannelli Filament 500 in produzione (manifest Geo assente) | Risolto, da rilasciare | `/fixcity/admin/login` risponde 200 su fixcity.eu |
| PROD-02 | `APP_DEBUG` attivo in produzione: stack trace e percorsi pubblici | Persona | Un errore mostra la pagina 500 del tema, senza trace |
| SEC-01 / OPS-14 | `APP_KEY`, token Matomo e credenziali di altri tenant committati | Persona | Chiavi ruotate; `git ls-files` non contiene env reali né config con segreti |
| SEC-02 | Callback SPID senza verifica firma SAML (latente: provider del tema non registrato) | Risolto (gate) | `php artisan route:list --name=spid` vuoto in produzione |
| PRD-03 / SEC-09 | Pagine demo `/tests/*` pubbliche in produzione | Risolto, da rilasciare | `/it/tests` risponde 404 su fixcity.eu |
| OPS-10 | `deploy.sh`: `composer update` in produzione, nessuna manutenzione, nessun backup | Parziale: manutenzione e build Geo fatte | Deploy con lockfile, backup prima di `migrate`, rollback documentato |
| DB-02 | `down()` di migrazioni di allineamento cancella `tickets` | Risolto per le due migrazioni 2026_10_22 | Nessuna migrazione non-create eredita il drop |
| DB-01 | Migrazione `citizen_rating` cancellata: database nuovi rotti | Risolto | `migrate:fresh` crea le due colonne |

## P1 — Essenziali

| ID | Problema | Stato | Criterio di accettazione |
| --- | --- | --- | --- |
| QA-01 | Test di moduli e temi fuori da ogni testsuite | Aperto (tentativo ritirato: helper `runTicketSeeder()` duplicato, `Pest.php` dei moduli non caricati) | `vendor/bin/pest` esegue tutte le testsuite senza fatal |
| OPS-02 / OPS-01 / OPS-03 | CI con `|| true`, senza dipendenze dei moduli, `tests.yml` rotto | Aperto | Un test rotto fa fallire la CI su `dev` |
| OPS-07 | Nessun lockfile, 79 vincoli `*`, `minimum-stability: dev` | Persona (decisione) | `composer.lock` versionato o equivalente riproducibile |
| SEC-08 | Stato e priorità modificabili dal browser nella modifica libera | Risolto | Test Livewire su `EditTicket`: stato invariato dopo `set` + `save` |
| SEC-03 | Tenant e DB scelti dall'header Host, `TRUSTED_HOSTS` vuoto | Persona + Aperto | `TRUSTED_HOSTS` impostato in produzione; test su host non ammesso |
| SEC-04 / DB-19 | Allegati PDF e foto di ticket non pubblici serviti da percorsi enumerabili | Aperto | Le API restituiscono solo immagini di ticket pubblici; media privati fuori dal disco `public` |
| SEC-05 | Account cancellati ancora utilizzabili via API e token | Aperto | Login API e token rifiutati per utenti con `deleted_at` |
| PRD-02 / SEC-14 | Email marcata verificata alla registrazione | Aperto | Verifica email reale prima di ricevute e invio ticket |
| PRD-04 | Informativa e dichiarazione di accessibilità con segnaposto `[[ente.*]]` | Persona (testi) + Aperto | Il wizard rifiuta l'invio se i dati dell'ente mancano |
| OPS-13 / DB-16 | Worker e scheduler non configurati: niente notifiche né retention GDPR | Persona | Supervisor e cron documentati e attivi; health check su coda |
| OPS-11 | Backup DB parziale, nessun backup media, nessun restore provato | Persona + Aperto | Backup giornaliero DB+media e restore provato |
| OPS-12 | Nessun error tracking, `LOG_CHANNEL` ignorato | Aperto | Errori di produzione tracciati e notificati |
| DB-10 | Ticket creati da admin senza `code`: niente notifiche al cittadino | Aperto | Ogni ticket ha `code` univoco |
| DB-05 / DB-06 / DB-07 | Doppia notifica, creazione non atomica, cambi di stato senza lock | Aperto | Test su doppio invio e concorrenza |
| PRD-01 | SPID/CIE assenti (codice esistente non sicuro né funzionante) | Aperto, da validare | Integrazione con un SP/proxy certificato AgID |
| PRD-05 / PRD-10 | Pagine vietate nel tema e rotta inesistente in `pratiche` | Aperto | `NoSemanticFolioPageDirectoriesTest` verde |

## P2 — Qualità, affidabilità, performance

| ID | Problema | Stato |
| --- | --- | --- |
| DB-03 / DB-04 | Indici su `status`, `type`, `owner_id`, `created_at`, `code` (unique) e `ticket_activities.ticket_id` | Aperto |
| DB-12 / DB-13 / SEC-12 | GeoJSON non paginato e senza cache; N+1 sui media | Aperto |
| DB-11 | KPI e SLA calcolati su `updated_at` | Aperto |
| DB-15 / PRD-09 | Multi-comune: onboarding e separazione dati documentati | Aperto |
| PRD-07 | Oscuramento di volti e targhe prima della pubblicazione | Aperto |
| PRD-08 | Tipi di segnalazione e dati dell'ente configurabili dal pannello | Aperto |
| UX-01 / UX-03 / UX-05 | Accessibilità del wizard e della mappa, stati di errore | Aperto |
| UX-07 / UX-08 | Traduzioni de/es mancanti (circa 212 chiavi) | Aperto |
| QA-09 / QA-10 / QA-11 | PHPStan anche su `app/` e temi; rapporti coerenti; Rector attivo | Aperto |
| OPS-08 | 16 vulnerabilità npm alte in `laravel/` e 24 alla radice | Aperto |
| OPS-17 | Marcatori di conflitto in 56 file `.md` | Aperto (il `.gitignore` del tema è risolto) |
| OPS-16 / OPS-18 / OPS-21 | 442 `.backup`, screenshot pesanti, 26.518 `.md` (1.379 duplicati) | Aperto |
| OPS-20 / PRD-14 | README di radice e di `laravel/` sbagliati | Aperto |

## P3 — Evoluzione

| ID | Proposta | Valore |
| --- | --- | --- |
| DB-17 | Collegare `ClassifyTicketAction` al wizard per suggerire il tipo | Riduce l'attrito del cittadino, già implementata a metà |
| DB-18 | Deduplicazione per distanza, tipo e finestra temporale | Riduce il carico degli operatori |
| PRD-12 | Integrazioni PA: protocollo, PEC in uscita, notifiche app IO | Requisito di vendita, da validare |
| PRD-13 | Togliere AdSense e "Spazio in vendita" dal tema PA | Coerenza istituzionale |

## Roadmap

1. **Stabilizzazione (P0).** Rilasciare `dev`; correggere env di produzione; ruotare i segreti; backup prima del deploy.
   Fatto quando: back office 200, nessun debug, nessun segreto valido nel repo, `/it/tests` 404.
2. **CI affidabile (P1 qualità).** Lockfile, CI che installa i moduli e migra un DB di test, gate senza `|| true`, workflow obsoleti rimossi.
   Fatto quando: un test rotto blocca la CI e la CI esegue tutte le testsuite.
3. **Completamento funzionale (P1 prodotto).** Verifica email, dati dell'ente obbligatori, `code` su ogni ticket, notifiche singole,
   creazione atomica, pagine vietate rimosse. Fatto quando: i journey (a)-(f) di PRODUCT_UX_AUDIT girano in un test end-to-end.
4. **Operatività.** Worker, cron, error tracking, backup DB e media, restore provato, rollback documentato.
5. **Ottimizzazione (P2).** Indici, GeoJSON paginato e in cache, accessibilità, traduzioni, pulizia del repository.
6. **Evoluzione (P3) e vendita.** SPID/CIE tramite soggetto certificato, integrazioni PA, triage AI e deduplicazione, qualificazione ACN.

Le fasi 1 e 2 bloccano tutte le altre: senza CI che esegue i test, nessuna correzione delle fasi successive è verificabile.
