---
title: "DCP-417: limiti effettivi PHP e nginx di fixcity.eu per gli allegati"
type: story
status: blocked
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, upload, fotocamera, php, nginx, deploy]
qmd: "limiti upload php nginx client_max_body_size post_max_size upload_max_filesize fixcity.eu produzione allegati"
related:
  - ../bmad/stories/mobile-camera-upload.story.md
  - ./dcp-420-fixture-di-prova-con-limiti-php-predefiniti.story.md
issues: []
discussions: []
---

# DCP-417: limiti effettivi di fixcity.eu

Bloccata: serve accesso al server di produzione (altro host, 46.225.90.178) e l'autorizzazione dell'utente a leggere o cambiare la configurazione.

## Origine

Story madre: [mobile-camera-upload](../bmad/stories/mobile-camera-upload.story.md), task «verificare e allineare i limiti effettivi PHP/FPM, web-server, proxy, Livewire e storage». La riduzione nel browser evita il problema per le foto, ma PDF fino a 10 MB (`TicketForm`, `maxSize(10*1024)`) e le foto che la riduzione rifiuta passano comunque dal server.

Misurato in sviluppo con i limiti predefiniti di PHP (`upload_max_filesize=2M`, `post_max_size=8M`): foto da 6,4 MB danno 422 con la chiave `validation.uploaded`, foto da 9,4 MB danno 413 (`PostTooLargeException`). Su fixcity.eu il server e' nginx e `client_max_body_size` non e' mai stato letto (se non impostato, nginx usa 1m).

## Acceptance criteria

1. I valori effettivi di produzione sono registrati con comando e output: `upload_max_filesize`, `post_max_size`, `max_file_uploads` del PHP-FPM che serve il sito, `client_max_body_size` di nginx (a livello `http`, `server` e `location`), e l'eventuale proxy o CDN davanti con un limite proprio.
2. I valori sono coerenti: nginx >= `post_max_size` >= `upload_max_filesize` >= 12M. Il 12M/16M e' gia' quello scelto in `public_html/.user.ini`; Livewire valida `file|max:12288`. Se la produzione e' diversa, la differenza e' documentata, non assunta.
3. Si sa se `.user.ini` agisce in produzione (vale solo con PHP in modalita' CGI/FastCGI; misurato: non e' letto da `php -S`). Se no, il valore sta nella configurazione del pool o in `php.ini`.
4. Una prova end-to-end su fixcity.eu con un PDF vicino a 10 MB e una foto grande riporta l'esito HTTP e il messaggio visto dal cittadino.
5. I requisiti (valori e dove si impostano) stanno in una pagina di docs del modulo Fixcity, senza date nel nome.

## Tasks

- [ ] Chiedere all'utente accesso in lettura (o i file di configurazione) e l'autorizzazione per la prova.
- [ ] Leggere i valori del criterio 1 e allegare l'output alla story.
- [ ] Proporre le differenze rispetto al criterio 2; applicarle solo dopo conferma.
- [ ] Prova end-to-end del criterio 4 con un account di prova.
- [ ] Scrivere la pagina di docs e collegarla dalla story madre.

## Verifica richiesta

Output reale dei comandi sul server, non dedotto dal repo. Nessuna modifica alla produzione senza conferma esplicita dell'utente.

## Owned file scope

Solo docs in `laravel/Modules/Fixcity/docs/`. La configurazione del server sta fuori dal repo.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato |
