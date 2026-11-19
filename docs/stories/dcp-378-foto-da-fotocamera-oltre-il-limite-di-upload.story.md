---
title: "DCP-378: foto da fotocamera rifiutate dal limite di upload (2 MB)"
type: story
status: review
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, upload, fotocamera, mobile, livewire, nginx, php, heic, validazione, traduzioni]
qmd: "foto fotocamera mobile upload fallito caricamento non riuscito the image failed to upload client_max_body_size upload_max_filesize 413 livewire"
related:
  - ./dcp-182-riepilogo-wizard-anteprima-delle-foto.story.md
  - ./dcp-128-wizard-passo2-cablaggio-design-comuni.story.md
  - ../../resources/views/filament/forms/components/dc-file-upload.blade.php
---

# DCP-378: foto da fotocamera rifiutate dal limite di upload

## Richiesta

Da mobile, una foto scattata con la fotocamera nel wizard di segnalazione mostra
«Caricamento non riuscito, riprova» e «The image failed to upload».
La foto e' il cuore della segnalazione: questo flusso non deve fallire.

## Analisi (prove)

1. Log nginx, 2026-10-27 16:58, dal wizard `/it/tickets/create`:
   `client intended to send too large body: 4441733 bytes ... POST /livewire-*/upload-file` (HTTP 413).
2. Limiti effettivi del server (impostati da Ploi, file di root):
   `/etc/nginx/conf.d/upload.conf` `client_max_body_size 2M`; PHP-FPM 8.4 `upload_max_filesize=2M`, `post_max_size=2M`.
3. Il campo `images` (`TicketForm`) promette 10 MB per file e il client `dc-file-upload` controlla 10 MB:
   una foto da 3-8 MB passa il controllo del browser e muore sul server.
4. `$wire.upload()` riceve l'errore, il componente mostra `error_upload`; Livewire chiama `_uploadErrored`,
   che cerca `validation.uploaded`. In italiano la chiave manca in `Modules/Xot/lang/it/validation.php`,
   quindi Livewire usa il suo testo inglese di riserva («The :name failed to upload»).

Il server rimuove gia' EXIF e GPS dalle foto (`StripTicketPhotoMetadataListener`): ridurle nel browser non perde dati.

## Decisioni (Ponytail)

| Problema | Soluzione | Perche' |
|---|---|---|
| Foto oltre il limite del server | Il client ridimensiona le immagini grandi (lato lungo 2048 px, JPEG) prima dell'upload | Funziona con qualunque limite server, meno dati mobili, upload piu' rapido in 4G. Riusa la pipeline canvas gia' presente per HEIC |
| Il client promette 10 MB ma il server ne accetta 2 | Il limite mostrato e controllato nel browser e' `min(maxSize, UploadedFile::getMaxFilesize())` | Un PDF da 5 MB riceve un messaggio chiaro invece di un errore generico |
| Messaggio in inglese | Aggiungere `uploaded` al set italiano di Xot | Unica fonte delle traduzioni di validazione |
| Limite di sistema 2 MB | Alzarlo da Ploi (Server, Settings, upload size) a 20 MB | Serve per i PDF; richiede root, quindi va fatto dal pannello |

Scartato: `.user.ini` per PHP (nginx blocca comunque a 2 MB); dipendenze JS nuove (basta il canvas).

## Criteri di accettazione

- [x] `trans('validation.uploaded', ..., 'it')` restituisce «Il caricamento di foto non è riuscito.» (tinker; test in `ValidationMessagesTest`).
- [x] Il componente espone come limite il minimo fra campo e server: `maxBytes: 2097152` sul server di produzione (test in `WizardUploadHelpTextTest`).
- [x] Una foto JPEG da 9,9 MB (4000x3000, EXIF Orientation=6) esce dal client a 1,3 MB, 1536x2048, ruotata correttamente, in 0,9 s.
- [ ] HEIC: stessa riduzione dopo il convertitore del tema; non verificato (Chrome headless non decodifica HEIC). Da provare su iPhone reale.
- [ ] Ploi: upload size portato a 20 MB (azione del proprietario, serve root). Serve per i PDF oltre 2 MB.

## Verifica

- Codice reale: HTML del wizard renderizzato da Laravel (`Livewire::test(CreateTicketWizardWidget::class)->html()`), oggetto Alpine `x-data`
  estratto ed eseguito in Chrome headless 155 con profilo `iPhone 13` (playwright-core). Misurati peso, dimensioni e pixel del marker.
- Il giro end-to-end nel wizard live non e' stato fatto: richiede un utente loggato e non si creano utenti in produzione senza approvazione.
- Pest non eseguibile su questo server: `.env.testing` usa l'utente DB della macchina dev e non esistono database `_test`
  (l'utente `fixcity_usr` ha permessi solo su `fixcity_data` e `fixcity_user`). Test scritti per CI e sviluppo.
- PHPStan sui file PHP toccati: 0 errori. Pint applicato.

## Note operative per altre sessioni

- Chrome headless senza root: le librerie mancanti sono estratte da `.deb` (`apt-get download`) in `~/.cache/pw-libs/root`;
  si avvia con `LD_LIBRARY_PATH` sulle cartelle `*.so*` e `executablePath` di `chromium_headless_shell-1247`.
  Il server MCP Playwright non vede quel `LD_LIBRARY_PATH`, finche' non si installano le dipendenze con `sudo npx playwright install-deps`.
