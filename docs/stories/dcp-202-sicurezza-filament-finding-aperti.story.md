---
id: dcp-202
title: "DCP-202: sicurezza del back office e delle API: finding aperti dopo gli audit DCP-180 e DCP-181"
type: story
epic: dc-parity-v2
status: ready-for-dev
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-15
updated: 2026-10-15
qmd: "sicurezza filament finding aperti B-2 C-1 C-3 D-2 allowlist upload rich editor blade render rating evaluator saveEnv EnvWidget EditUserWidget passport cors rate limit tickets.json"
related: [dcp-180, dcp-181, dcp-186, dcp-197, dcp-200]
tags: [sicurezza, filament, audit, owasp]
---

# DCP-202: sicurezza del back office e delle API: finding aperti dopo gli audit DCP-180 e DCP-181

## Fase BMAD
Plan, poi Implement a test rosso prima. I rapporti sono `Modules/Fixcity/docs/security/filament-security-audit-altri-pannelli.md` (DCP-181) e la story DCP-180. Corretti finora: A-1, A-2, A-3, B-1, C-2, C-4, D-1, D-3 e F-01..F-03. Qui restano i **finding aperti** e quelli emersi dall'audit del front office (DCP-186).

## Da fare (ogni voce: test che fallisce sul difetto, poi correzione, poi PHPStan a zero)
- [ ] **B-2, allowlist dei tipi sui campi di upload.** 28 campi in 11 moduli accettano qualunque tipo su dischi serviti dal web. Regola globale come per il bulk delete: un default in `FileUpload::configureUsing()` (immagini jpg/png/webp, pdf) e uno stesso elenco nel validatore server; i campi che hanno bisogno d'altro lo dichiarano. Se i tipi per campo non sono ovvi, si apre una riga nuova in DCP-200 prima di scegliere.
- [ ] **C-1, RichEditor dei blocchi CMS stampato con `{!! !!}`.** Passare da `sanitizeHtml()` (o `Str::sanitizeHtml`) in lettura; test con `<script>` e `onerror=`.
- [ ] **C-3, `Blade::render()` su contenuto CMS che contiene `{{ }}`.** Un editore puo' eseguire espressioni Blade. Sostituire con rendering di sola sostituzione di variabili note, oppure escapare le graffe; test con `{{ phpinfo() }}`.
- [ ] **D-2, lista Filament di `RatingMorph`.** La policy dice che l'evaluator vede solo le proprie valutazioni sul singolo record, ma la lista non lo applica: aggiungere lo scope alla query della risorsa; test con due evaluator.
- [ ] **Quattro piste fuori categoria, da leggere e classificare** (ognuna: leggere il codice, dire se e' un difetto, poi test o chiusura motivata):
  - [ ] `JobStatusWidget::saveEnv` (scrive il file `.env` da un widget);
  - [ ] `EnvWidget` (mostra o modifica variabili d'ambiente);
  - [ ] `EditUserWidget` (campi modificabili: assegnazione di massa o ruoli?);
  - [ ] Passport `revoke_all_for_user` (chi puo' chiamarlo?).
- [ ] **API pubbliche (da DCP-186, B19 e B7).** `/tickets` e `/api/tickets/geojson` senza limite di richieste e con `Access-Control-Allow-Origin: *`: limite per IP e origine ammessa dalla configurazione. `/api/ticket-details/{id}` ricade su `public_html/data/tickets.json` (l'id 1 risponde 200 con dati finti): togliere il ripiego. Decisione: DEC-24 (proposta: tutte e tre).
- [ ] **Esecuzione di `.php` nelle cartelle caricate.** Non e' stato verificato se il web server nega l'esecuzione sotto `/storage` e `/uploads`: provarlo in produzione (la regola da dare al fornitore e' in DCP-208).
- [ ] **Pannello `fixcity/admin` aperto nel browser** con l'utente master (non fatto: i test sono solo Pest).
- [ ] **Credenziali nella storia di git** (DEC-28): chiedere la rotazione della password FTP del disco `photos` e della chiave Supermemory; **non** riscrivere la storia.
- [ ] `composer audit` e `npm audit` ripetuti a ogni rilascio (oggi: 0 avvisi per Composer; npm non misurato in questa passata).

## Gia' coperto da altre story (non duplicare)
Esposizione per ambiente, repliche `tests`, sitemap con l'header `Host`, soft 404: DCP-197 (Sixteen).

## Criteri di accettazione
- [ ] Per ogni voce un test rosso prima e verde dopo, nominato sul comportamento.
- [ ] PHPStan `Modules` a zero, Pint solo sui file toccati, mai su una cartella di modulo.
- [ ] Rapporto di DCP-181 aggiornato con lo stato di ogni finding (aperto/corretto/accettato con motivo).

## Non verificato
Il pannello `fixcity/admin` nel browser; `npm audit`; il comportamento del web server sui file caricati; i dischi di produzione.
