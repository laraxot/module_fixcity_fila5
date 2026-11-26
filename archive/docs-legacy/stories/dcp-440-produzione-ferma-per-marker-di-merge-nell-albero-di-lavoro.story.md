---
title: "DCP-440: produzione ferma (HTTP 500) per marker di merge nell'albero di lavoro"
type: story
status: needs-decision
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, produzione, merge-marker, parseerror, sync, urgente]
qmd: "fixcity.eu 500 parseerror merge_file marker di merge produzione ferma sync tickets/2 foto"
related:
  - ../bmad/stories/CAM-1.6-public-ticket-photo-gallery.story.md
  - ./dcp-377-controllo-qualita-giro-front-office-e-sync-git.story.md
issues: []
discussions: []
---

# DCP-440: produzione ferma per marker di merge

## Richiesta

Utente, 2026-10-27: «in https://fixcity.eu/it/tickets/2 dovrei vedere anche la foto che ho fatto upload con la camera del cellulare, invece vedo File: grcpCs...jpg ... la sezione delle foto dovrebbe essere sotto la mappa». La parte foto e' la story [CAM-1.6](../bmad/stories/CAM-1.6-public-ticket-photo-gallery.story.md) (un'altra sessione, stessi file, gia' implementata alle 20:47). Controllando la pagina e' emerso di peggio.

## Misurato (2026-10-27, circa 20:46)

`curl` in sola lettura su fixcity.eu: **HTTP 500 su tutte le 7 URL provate** (`/`, `/it`, `/it/tickets`, `/it/tickets/2`, `/it/tickets/create`, `/it/lista-categorie`, `/it/auth/login`). Il corpo (895 KB) e' un `ParseError` il cui estratto di codice contiene `merge_file_ynexSH` e `merge_file_clVsMZ` attorno a `->maxSize(10 *`: un file PHP del server di produzione ha dentro i marker di conflitto del tool di sync.

Il sintomo dell'utente («File: ...jpg») non e' piu' raggiungibile: nessuna pagina del sito si apre. Va letto come il sintomo di prima del guasto; la causa della foto assente e' nel codice (la vista non aveva nessuna sezione foto, vedi CAM-1.6).

## Cosa NON e' la causa (verificato)

- Nei repo locali `HEAD` e' pulito: nessun commit di `Fixcity`, `Xot`, `Media`, `Sixteen` contiene marker, e i commit non spediti sono 0.
- Il suffisso `ynexSH` non esiste in questo albero: i marker sono sul server di produzione (altra macchina, vedi memoria `other-machine-auto-sync-deletes-stubs-and-marker-commits`), non vengono da un push di questa sessione.
- Il server NON serve questo albero (ipotesi del filesystem condiviso, provata e smentita alle 20:57): `Last-Modified` di prod contro mtime locale in `public_html/`: `robots.txt` 27 ott 14:52 contro 26 ott 10:28; `favicon.ico` 17 ott 07:37 contro 24 set 06:52; `manifest.json` 17 ott 08:45 contro 17 ott 08:33. Ha un suo checkout, con le sue ondate di marker (suffissi `ynexSH`, `clVsMZ` alle 20:46, `h3NVzL`, `ORZcxL` alle 20:56).

## Stato di produzione, rilevato a ondate

| Ora | `/` | `/it/lista-categorie` | `/it/tickets/2` |
|-----|-----|-----------------------|-----------------|
| 20:46 | 500 | 500 | 500 |
| 20:56 | 302 | 200 | 500 (`ParseError`, marker `h3NVzL`/`ORZcxL` in un array) |

| 21:12 | 302 | 200 | 404 da anonimo |

Alle 20:56 la ripresa era parziale e i marker locali si spostavano tra i file della galleria foto. Alle 21:12 il sito risponde: 6 URL su 7 con 200 o 302 (`/it`, `/it/tickets`, `/it/lista-categorie`, `/it/auth/login` 200; `/` e `/it/tickets/create` 302). Non e' stata un'azione di questa sessione: il server ha ripulito il suo checkout da solo o per mano di un'altra sessione. La causa (nessun controllo anti-marker prima del deploy) non e' rimossa: ogni nuova ondata riporta il 500.

Osservazioni misurate alle 21:12:

- `/it/tickets/2` risponde 404 a un anonimo, non 500. L'Action `BuildPublicTicketDetailAction` mostra a tutti solo gli stati pubblici (`TicketStatusEnum::canViewByAll()`) e al proprietario la propria segnalazione in qualunque stato: un 404 da anonimo per un ticket che l'utente vede da loggato indica che la segnalazione 2 non ha uno stato pubblico. Quindi per il caso dell'utente la foto serve **al proprietario**, la parte di CAM-1.6 gia' prevista dalla decisione D230-5 (originale al proprietario), non la galleria pubblica.
- Scansione locale dei marker con il binario `/usr/bin/grep -r` su Fixcity, Xot, Media, User e Sixteen (codice, lang, json, blade; `docs/` escluso): 0 file. Attenzione: la funzione di shell `grep` dello snapshot di Claude Code, usata con `-r`, restituisce **nessun risultato** anche dove il binario ne trova (verificato su `Modules/User/docs`); per le scansioni ricorsive usare `/usr/bin/grep`, `rg` o `git grep`.

## Marker presenti oggi nell'albero locale (non committati)

Locale, non produzione, ma e' lo stesso meccanismo e alcuni file sono in lavorazione di altre sessioni:

| Repo | File |
|------|------|
| Fixcity | `lang/{de,en,es,it}/ticket_form.php`, `resources/views/filament/forms/components/dc-file-upload.blade.php`, `tests/Feature/Filament/CreateTicketWizardDataStepTest.php` |
| Sixteen | `resources/js/app.js`, `resources/views/components/pages/auth-login.blade.php`, `public/manifest.json`, `docs/prompts/04.md` |
| Xot | `lang/it/validation.php` (alle 20:44 `php artisan` non partiva per questo; vedi DCP-437) |

Non li ho toccati: contengono lavoro vero dentro i marker (regola della memoria `merge-file-false-conflicts-restore-head-after-proof`: si ripristina da HEAD solo dopo aver provato che il conflitto e' falso).

## Decisioni per l'utente

1. **Produzione**: chi ripristina i file sul server 46.225.90.178? Serve accesso che questa sessione non ha, e il ripristino e' un'azione sul sito pubblico (da confermare). Raccomandazione: sul server individuare i file con `grep -rlE '^(<<<<<<<|>>>>>>>) ?\.?merge_file'`, `git checkout -- <file>` solo dopo aver visto che nessun lavoro non committato va perso, poi `php artisan view:clear` e `config:clear`.
2. **Sync**: sospendere l'auto-sync del server finche' non e' sistemato? La causa e' ripetuta (DCP-377, memoria sul commit `.`); un controllo prima di ogni deploy (`php -l` sui file toccati e `grep` dei marker) la chiuderebbe.
3. **Debug in produzione**: la pagina d'errore mostra il codice sorgente. Se `APP_DEBUG` e' acceso in produzione e' una fuga: da verificare sul server con l'utente (qui non si leggono ne' si stampano `.env` o valori).

## Acceptance criteria

1. Le 7 URL sopra rispondono 200 (o 302 verso il login per le pagine protette) da un client anonimo, misurato con `curl`.
2. Nessun file `.php` del server di produzione contiene `merge_file`: `php -l` pulito sull'intero albero.
3. Una pagina che genera un errore a un visitatore mostra la pagina 500 a tema, senza codice sorgente (`APP_DEBUG=false` verificato dall'utente).
4. Il controllo anti-marker prima del deploy e' scritto nel documento di rilascio, non solo in questa story.

## Tasks

- [ ] L'utente decide chi ripristina il server e dice se posso farlo io (accesso).
- [ ] Ripristino dei file e `view:clear`/`config:clear`.
- [ ] Rilettura delle 7 URL e consegna dell'output qui sotto.
- [ ] Marker locali: ciascuna sessione proprietaria risolve i propri (upload: sessione camera; Xot: DCP-437).
- [ ] Controllo anti-marker nel gate di rilascio.

## Owned file scope

Nessun file di codice. Documentazione: questa story, il documento di rilascio quando esiste.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 20:48 | Claude (sessione camera upload) | misura e stesura | nessun file toccato, nessun accesso al server; la galleria (CAM-1.6) e' di un'altra sessione |
| 2026-10-27 20:51 | Claude (sessione a9bceaa6, upload e validation) | `Xot/lang/it/validation.php` risolto | Marker alle 20:43:47 su `uploaded`: lato 1 = frase intera dell'albero, lato 2 = riga vecchia di HEAD con `:attribute`. Tenuto il lato 1 (e' quello letto da `ValidationMessagesTest` e da DCP-437); `php -l` ok, il dev 8081 passa da 500 a 404/200. Le **ondate di marker continuano** (20:43:46, 20:47:47, 20:48:29) e ora toccano anche `BuildPublicTicketDetailAction`, `BuildTicketTimelineAction`, `design-comuni.blade.php`, `BuildPublicTicketDetailActionTest`, `TicketForm.php`. Nessun processo di merge in `ps`, nessun cron o timer: lo scrittore non e' un processo di questa macchina oppure dura meno di un secondo. Nei file della fotocamera i lati sono **due linee di lavoro vere**, non risolvibili con HEAD: client del tema (`FixcityCameraUpload`, `Carica file` e `gallery_button`) contro client Media (`MediaCameraImage`, `Scatta una foto` e `camera_button`). La scelta va fatta tra le sessioni che le possiedono. |
| 2026-10-27 21:04 | Claude (sessione 6d2caca8, PHPStan e foto) | origine dei marker: misura da questa macchina | La riga di 492b606a in DCP-379 dice che il processo `claude --dangerously-skip-permissions` pid 2372069 fa `git push laraxot HEAD:dev` e i merge, su «questa macchina, che e' la produzione (nginx root `/home/ploi/fixcity.eu/public_html`)». **Su `zorin` non e' cosi':** `hostname` = `zorin`, il pid 2372069 non esiste e `/home/ploi/fixcity.eu/public_html` non esiste. Quindi la riga descrive il server, e va letta cosi': **sul server di produzione gira almeno una sessione Claude con permessi saltati che modifica, committa e spinge il codice servito al pubblico**, e i suoi push fondono con i nostri su `laraxot/dev`. E' la spiegazione piu' semplice del ciclo di circa 4 minuti (20:43:48, 20:47:47, 20:51:52, 20:55:5x) e dei 500 di fixcity.eu: non e' un difetto dello script di sync, e' un secondo gruppo di agenti che scrive negli stessi file. Decisione per l'utente (nuova, la 4): **dove si sviluppa?** Il codice servito non dovrebbe essere modificato da un agente in corso d'opera; serve un solo punto di scrittura e il server che fa solo `pull`. Misura PHPStan sul nostro albero: 0 errori alle 21:02, quindi i 500 non vengono da codice sbagliato ma da file fusi a meta'. |
| 2026-10-27 21:08 | Claude (sessione 1564de94, camera upload e PHPStan) | `Themes/Sixteen/public/manifest.json` rigenerato; PHPStan 188 a 0 | Il manifest (file generato, 12 marker, fermo dalle 20:47:47) mandava in 500 home, elenco, categorie e login di sviluppo con «Unable to locate file in Vite manifest: resources/css/app.css». Non risolto a mano: `npm run build` nel tema, nessun `vite build` concorrente (la build e' additiva, `emptyOutDir:false`). Dopo: JSON valido, 60 voci, 0 asset mancanti, 0 immagini a zero byte, e `/it`, `/it/tickets`, `/it/lista-categorie`, `/it/auth/login`, `/en`, `/it/tickets/track` = 200 su :8081. Alle 20:53 nessun marker in codice, config e lang. PHPStan `analyse Modules` alle 21:07: exit 0, 0 righe; il dettaglio dei 188 di prima (marker, `env()` nelle config del modulo, test non Safe, chiave doppia) e' nella story Xot `phpstan-modules-swarm-random`. **Due limiti.** (1) Il manifest e' tracciato in git: se il server fonde ancora i file tornera' con i marker, la build cura il sintomo e non la causa (righe 86 e 87). (2) La ricomposizione della fotocamera ha tenuto il client Media: il bundle servito contiene `MediaCameraImage` (soglia 1 MB, lato 1920, qualita' fissa 0,85, nessun ciclo sul peso) e non piu' `FixcityCameraUpload` (ciclo fino a 900 KiB). Le mie misure del 17:35 (6,2 MiB a 713 KiB, rumore 8,65 MB a 904 KiB) valgono per il client scartato, **non** per quello in uso: con il default di nginx a 1 MB il caso peggiore non e' stato riprovato. |
