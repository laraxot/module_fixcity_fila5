---
title: "DCP-420: fixture di prova con i limiti PHP predefiniti"
type: story
status: ready-for-dev
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, fixture, upload, strumenti-di-prova]
qmd: "scratch-auth-fixture upload_max_filesize post_max_size maschera il difetto limiti predefiniti php -S sonda"
related:
  - ../bmad/stories/mobile-camera-upload.story.md
  - ./dcp-47-fixture-autenticata-misure-wizard.story.md
issues: []
discussions: []
---

# DCP-420: fixture con limiti predefiniti

## Origine

`bashscripts/tools/visual/scratch-auth-fixture.sh` (riga 128) avvia `php -S` con `-d upload_max_filesize=12M -d post_max_size=16M`. Con quei valori una foto da 6,4 MB passa (200) e il difetto dell'upload da fotocamera non si vede; sul server di sviluppo e in molti server reali i limiti sono 2M/8M e la stessa foto fallisce. Per riprodurre ho dovuto clonare l'ambiente su un'altra porta a mano. Ne parla la story [DCP-47](./dcp-47-fixture-autenticata-misure-wizard.story.md) come strumento di prova condiviso.

## Acceptance criteria

1. Il fixture accetta un'opzione (variabile d'ambiente, nome da decidere in fase di sviluppo) che omette i due `-d`, cioe' usa i limiti del `php.ini` della macchina.
2. Senza l'opzione il comportamento resta quello di oggi: altre sessioni usano il fixture per i loro e2e e non devono accorgersene.
3. L'opzione e' descritta in `bashscripts/docs/` (policy: niente docs accanto agli script in radice).
4. Il fixture stampa all'avvio quali limiti sta usando, cosi' una prova non e' mai ambigua.
5. Una sonda di esempio per l'upload (foto 6,4 MB e 9,4 MB) e' documentata con il risultato atteso su un server con i limiti predefiniti: 422 e 413 senza la riduzione, 200 con la riduzione.

## Tasks

- [ ] Rileggere `scratch-auth-fixture.sh` (un'altra sessione l'ha modificato) e `bashscripts/docs/` per la pagina giusta.
- [ ] Aggiungere l'opzione e la riga di log dei limiti.
- [ ] Provare l'avvio nei due modi su una porta libera (controllare con `ss -ltn`).
- [ ] Documentare.

## Verifica richiesta

Output di due avvii reali con i limiti letti da `ini_get()` dentro la richiesta, non dedotti dai flag. Non fermare ne' riavviare i fixture in uso da altre sessioni (:8091 e :8092): provare su una porta nuova.

## Owned file scope

`bashscripts/tools/visual/scratch-auth-fixture.sh`, una pagina in `bashscripts/docs/`.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato |
