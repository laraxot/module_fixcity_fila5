---
story_id: FIXCITY-MEDIA-001
title: "Segnalazione: upload foto dalla fotocamera mobile"
type: story
status: in-progress
epic: civic-reporting
priority: P1
owner: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, fixcity, media, mobile, camera, upload, i18n]
---

# Story

Come cittadino che usa uno smartphone, voglio scattare o selezionare una foto
nel wizard di una segnalazione e inviarla senza l’errore generico di upload,
così posso documentare il disservizio senza uscire dal flusso.

## Acceptance Criteria (LOCKED)

- [ ] L’input offre la cattura nativa della fotocamera posteriore e mantiene
      sempre il percorso alternativo per selezionare un file.
- [ ] JPEG, PNG, HEIC/HEIF e PDF seguono percorsi espliciti; HEIC/HEIF viene
      normalizzato in JPEG prima dell’upload Livewire quando il browser lo
      consente.
- [ ] La normalizzazione è realmente collegata al campo usato dal wizard:
      il file normalizzato, non quello originale, arriva a `$wire.upload()`.
- [ ] Il limite applicativo resta 10 MB per file; i limiti PHP, web-server,
      proxy, Livewire e storage sono compatibili e verificati nel runtime
      attivo.
- [ ] Il server verifica MIME e contenuto, non soltanto estensione o MIME
      dichiarato dal browser; un file contraffatto viene rifiutato.
- [ ] Cancel, replace, retry, sessione scaduta e upload temporaneo fallito non
      creano media o ticket parziali/orfani.
- [ ] Deny permission, HTTPS assente, camera non disponibile, rete interrotta
      e conversione fallita mostrano un errore accessibile e localizzato in
      it/en/de/es, con fallback al picker file.
- [ ] Nessuna nuova stringa visibile è hardcoded; il flusso conserva label,
      stato, errore e successo accessibili senza dipendere dalla fotocamera.
- [ ] Il comportamento è verificato a 375 px, 768 px e 1440 px e su iPhone
      Safari HTTPS, Android Chrome HTTPS, Samsung Internet e desktop.

## Dev Notes (LOCKED)

- Il primo punto di errore è il temporary upload Livewire, prima della
  persistenza Spatie Media Library. Media possiede il contratto di MIME,
  dimensione, sicurezza, ownership e cleanup; camera API e normalizzazione
  preventiva appartengono al campo/theme.
  [Source: ../../../../Media/docs/bmad/mobile-camera-upload-investigation.md]
- `capture="environment"` abilita la cattura nativa, ma non risolve da solo
  HEIC, limiti runtime o validazione server.
  [Source: ../../../../Media/docs/bmad/mobile-camera-upload-investigation.md]
- Non introdurre Take Picture Field o Uppy finché il percorso nativo collegato
  al campo Livewire non è provato insufficiente: aggiungerebbero endpoint,
  autorizzazioni, cleanup e un percorso di persistenza distinto.
  [Source: ../../../../Media/docs/bmad/mobile-camera-upload-plan.md]
- Il campo attuale usa il componente `dc-file-upload`; il contratto Filament e
  la collezione `attachments` restano invariati.
  [Source: ../filament-blueprint.md]
- Tutti i messaggi devono usare le traduzioni del modulo Fixcity; non usare
  fallback testuali in Blade o JavaScript.
  [Source: ../../../../Themes/Sixteen/docs/bmad/DOC-FOLDERS-INDEX.md]

## Tasks / Subtasks

- [ ] Collegare `normalizeCameraImage()` al percorso reale del campo e a
      `$wire.upload()`; coprire il fallback senza decoder HEIC. (AC: #2, #3)
- [ ] Ridimensionare/comprimere le foto camera JPEG/PNG oltre la soglia utile,
      oltre alla conversione HEIC/HEIF. (AC: #2, #4)
- [ ] Verificare e allineare i limiti effettivi PHP/FPM, web-server, proxy,
      Livewire e storage senza ampliare la whitelist MIME. (AC: #4)
- [ ] Verificare Media temporary upload, ownership, MIME reale, cleanup e
      rollback quando il trasferimento o `saveRelationships()` fallisce.
      (AC: #5, #6)
- [ ] Aggiungere test di contratto per camera JPEG grande, HEIC/HEIF,
      MIME vuoto, MIME contraffatto, oversize, cancel/replace, sessione e
      cleanup. (AC: #2, #5, #6)
- [ ] Aggiornare le chiavi di traduzione it/en/de/es e verificare ARIA,
      tastiera e fallback file. (AC: #7, #8)
- [ ] Eseguire test applicativi da `laravel/`, build/lint del tema da
      `laravel/Themes/Sixteen` e `graphify update .`; completare la matrice
      browser/device quando sono disponibili HTTPS e dispositivi reali.
      (AC: #9)

## Testing strategy (LOCKED)

- Feature/Livewire: il file prodotto dalla fotocamera entra nel temporary
  upload e viene associato alla collezione `attachments`.
- Unit/contract Media: MIME basato sul contenuto, dimensione, ownership,
  session affinity, cleanup e rollback.
- Browser: permission denied, cancel, replace, retry, rete lenta e fallback
  picker; nessun testo visibile hardcoded.
- Responsive/accessibility: 375/768/1440 px, focus, `aria-invalid`,
  `aria-describedby`, live region e assenza di overflow.
- Runtime: PHP/FPM/web-server/proxy/Livewire/storage con valori effettivi,
  non solo configurazione versionata.
- Manual device gate: iPhone Safari HTTPS con HEIC/JPEG, Android Chrome con
  JPEG ad alta risoluzione, Samsung Internet e desktop.

## Dependency map

- Blocked by: disponibilità di HTTPS e dispositivi reali per la prova finale;
  valori runtime del reverse proxy/FPM; contratto Media temporary upload.
- Blocks: rilascio affidabile del wizard mobile e qualsiasi messaggio di
  completamento dell’iniziativa camera-upload.
- Supporting handoffs:
  - [Media contract](../../../../Media/docs/bmad/stories/fix-mobile-camera-upload.story.md)
  - [Sixteen integration](../../../../../Themes/Sixteen/docs/bmad/stories/camera-upload-mobile-fix.story.md)

## Executable child stories

La decomposizione BMAD operativa è in [Epic CAM-1](../epics/mobile-camera-upload.epic.md):

- [CAM-1.1 — runtime transport](./CAM-1.1-runtime-transport-contract.story.md)
- [CAM-1.2 — browser normalization + Livewire](../../../../../Themes/Sixteen/docs/bmad/stories/CAM-1.2-browser-normalization-livewire.story.md)
- [CAM-1.3 — Media contract + Imagick probe](../../../../Media/docs/bmad/stories/CAM-1.3-server-media-contract-imagick.story.md)
- [CAM-1.4 — translations + accessibility](./CAM-1.4-translations-accessibility.story.md)
- [CAM-1.5 — verification + release gate](./CAM-1.5-verification-release-gate.story.md)

## Owned File / Module Scope

- `laravel/Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`
- `laravel/Modules/Fixcity/resources/views/filament/forms/components/dc-file-upload.blade.php`
- `laravel/Modules/Fixcity/lang/{it,en,de,es}/ticket_form.php`
- `laravel/Modules/Fixcity/tests/Feature/`
- `laravel/Modules/Fixcity/tests/Unit/`
- Coordinamento del contratto in `laravel/Modules/Media/` e bundle in
  `laravel/Themes/Sixteen/` solo nei rispettivi handoff.

## Learnings from previous work

- Importare `camera-upload.js` nel bundle e aumentare i limiti PHP non dimostra
  il roundtrip: il campo deve invocare davvero la normalizzazione prima di
  `$wire.upload()`.
- Convertire soltanto HEIC è insufficiente per JPEG/PNG ad alta risoluzione
  prodotti dalla fotocamera.
- Un errore Livewire può avvenire prima di Media Library; il test deve quindi
  coprire entrambi i confini, non soltanto la persistenza finale.

## Coordination record

Questa story è la fonte operativa unica per claim, handoff, decisioni e
verifiche della feature. `docs/chat/` è archivio storico: non riceve nuove
note per questa iniziativa e non è una dipendenza per l’implementazione.

Review già ricevute dagli agenti: la precedente preparazione client-side non
dimostrava il collegamento al campo reale; il fix resta non approvato finché
non sono coperti i gate elencati sopra.

### Registro operativo (piu' recente in fondo)

- **17:28, sessione Claude 6d2caca8.** Letto il lavoro non committato e le story.
  Divisione proposta per non toccare gli stessi file:
  - **Altra sessione** (osservata nell'albero): correzione di `docs/chat` nelle
    regole `bashscripts/ai/{wiki,.agents}`, lang e view Fixcity, adapter camera
    di Sixteen. Questa sessione non li modifica.
  - **Questa sessione**: modulo Media, cioe' i task «Verificare Media temporary
    upload» e «test di contratto» lato server (MIME dal contenuto, MIME vuoto o
    contraffatto, oversize, ownership, cleanup), piu' la prova di runtime con i
    limiti PHP di default.
  - **Verifica finale**: grep delle istruzioni `docs/chat` rimaste nelle regole
    canoniche; i residui vengono segnalati qui, non corretti sopra il lavoro in
    corso dell'altra sessione.
- **Decisione aperta D1: cattura nativa o `getUserMedia`.** L'AC 1 chiede la
  cattura nativa (`capture="environment"`), il punto 3 del piano Fixcity chiede
  `getUserMedia` in modal. Sono due percorsi diversi. `getUserMedia` richiede un
  contesto sicuro (HTTPS o localhost): su `http://192.168.1.40` il browser lo
  blocca, mentre un input con `capture` funziona anche su HTTP. Il difetto
  segnalato («Caricamento non riuscito») e' un limite di dimensione del
  trasporto, che `prepare()` risolve per entrambi i percorsi. Raccomandazione:
  consegnare prima cattura nativa + `prepare()`, dopo aver provato che
  `normalizeCameraImage()` chiama davvero `prepare()` prima di `$wire.upload()`;
  trattare `getUserMedia` come passo successivo, solo se serve l'anteprima in
  pagina. Risposta dell'altra sessione attesa qui sotto.
- **2026-10-27, sessione Claude 6d2caca8: misura sul lato server di Media, senza
  modifiche al codice.** Previsione da smentire: `StripImageMetadataAction` (GD,
  sincrona nella richiesta di invio) manda in fatal di memoria le foto da 50 MP.
  Misurato con le stesse tre chiamate dell'Action (`decodeBinary`, `encode` con
  `strip`), PHP 8.4 CLI, GD 2.3.3 di sistema: 12, 24 e 50 MP finiscono senza
  errori anche con `memory_limit=128M` (picco PHP 24-28 MB; 50 MP in 1,2 s).
  Il fatal non c'e' qui perche' la libgd di sistema alloca fuori da
  `memory_limit`; costa pero' 460 MB di RSS per una foto da 50 MP, che il tetto a
  2048 px del browser riduce a una frazione. Quindi nessun guard sui pixel in
  Media. Non verificato: il PHP di produzione (altro server) puo' avere una GD
  diversa.
- **Decisione aperta D2: Imagick lato server (proposta dell'utente).** Aiuterebbe
  la sola conversione HEIC -> JPEG (`libheif` e' gia' nel sistema; Intervention
  ha gia' il driver Imagick) e un decode a bassa memoria. Non risolve il difetto
  segnalato: PHP respinge l'upload (422 sopra `upload_max_filesize`, 413 sopra
  `post_max_size`) prima che giri qualunque codice. Qui `ext-imagick` non e'
  installata, non e' in `composer.json` e non si sa se lo sia in produzione.
  Raccomandazione: tenere la riduzione nel browser (nessun requisito di
  infrastruttura, gia' provata con un HEIC vero); riconsiderare Imagick solo se
  l'owner vuole accettare HEIC sul server, dopo aver letto
  `Imagick::queryFormats('HEIC')` su fixcity.eu.
- **Agenti avviati da questa sessione:** test di contratto lato server (MIME
  contraffatto, MIME vuoto, JPEG troncato, Action di strip EXIF), revisione
  client indipendente, revisione di sicurezza. Gli esiti vengono aggiunti qui.
- **17:46, sessione Claude 6d2caca8: richiesta «crea tutte le bmad stories».**
  Non creo story nuove: tra le 17:42 e le 17:43 esistono gia' l'epic CAM-1
  (CAM-1.1..1.5) e una seconda serie `1.1..1.5`. Copertura verificata sulle
  consegne necessarie: trasporto -> CAM-1.1; client e collegamento reale ->
  CAM-1.2 (infrastruttura di test presente: Jest e Playwright nel tema);
  contratto server Media, file contraffatto o troncato, MIME vuoto, ownership,
  cleanup e probe Imagick -> CAM-1.3; messaggi e accessibilita' -> CAM-1.4;
  matrice dispositivi e rilascio -> CAM-1.5; EXIF -> story privacy gia' in
  `review`. D1 e' gia' risolta dall'epic («getUserMedia fuori scope finche' il
  percorso nativo non fallisce»); D2 e' formulata in CAM-1.3 come «Imagick solo
  se il probe passa». Quindi D1 e D2 si chiudono con queste due story, non con
  altre.
- **Problema aperto: due serie per la stessa feature.** `CAM-1.x` (nel
  formato dell'epic, collegate dall'epic) e `1.x` (`1.1.mobile-upload-wizard-contract`,
  `1.2.camera-upload-client-normalization`, `1.3.imagick-heic-fallback`,
  `1.4.image-processing-runtime`, `1.5.mobile-upload-quality-gate`) sono state
  scritte a 16 secondi di distanza e dicono in gran parte la stessa cosa; la
  mappa in fondo a questa story le elenca entrambe. «Una feature = una story»
  e «mai cancellare ne' rinumerare»: proposta di tenere CAM-1.x come canonica
  (e' l'unica collegata dall'epic) e marcare le `1.x` con `status: superseded`
  e un link alla CAM corrispondente, dopo aver controllato che nessun criterio
  delle `1.x` manchi nelle CAM (`1.4` e `1.3` Media vs `CAM-1.3`). Non lo faccio
  io: sono file dell'altra sessione, ancora in scrittura.
- **Esito della revisione client indipendente (36 chiamate di tool, letta la
  versione `camera-upload.js` 17:40 e `dc-file-upload.blade.php` 17:39).** Il
  collegamento c'e': entrambi gli input passano da `pick()` ->
  `normalizeCameraImage` -> `prepare()`, e a `$wire.upload()` arriva il file
  preparato. Chiavi di errore presenti in it/en/de/es. Rilievi, con la story
  che li possiede:
  - **CAM-1.1:** il tetto `targetBytes` e' `min(campo, 90% PHP, 900 KiB)`: con
    server a 12M/16M ogni foto scende comunque a 900 KiB e 2048 px, mentre l'AC
    dice 10 MB per file. E' una scelta prudente (nginx predefinito = 1 MB) ma
    va decisa, non lasciata implicita. Un PDF tra 900 KiB e il limite PHP puo'
    prendere un 413 di nginx.
  - **CAM-1.2:** senza `window.FixcityCameraUpload` il file parte non ridotto
    (blade:98) e il controllo di dimensione usa `maxBytes` invece di
    `targetBytes`; la costante `MAX_CANVAS_PIXELS` non e' mai vincolante con
    `maxEdge=2048`; nessun `console.warn` della causa nel `catch`; nessun test
    di `prepare()`; decodifica a piena risoluzione (48 MP ~190 MB di bitmap, non
    riprodotto su dispositivo).
  - **CAM-1.4:** mancano `aria-invalid` e `aria-describedby` verso l'errore;
    gli input nascosti restano nella tabulazione e il secondo non ha etichetta;
    `gallery_aria` non contiene il testo visibile (WCAG 2.5.3); gli stati
    `preparing`/`done` non sono annunciati; la chiave `error_camera` non e'
    usata da nessuno; il testo d'aiuto dice «1.8 MB» anche per le foto.
  - **Tech-spec** `tech-spec/mobile-camera-upload.md` (M1, M2, M6, M7) descrive
    ancora `getUserMedia`, 1280 px e camera=(self): da marcare `superseded`.
  - **HEIC e `accept` su iPhone (A, sorgente WebKit `WKFileUploadPanel.mm`):**
    con una lista `accept` esplicita Safari iOS consegna comunque JPEG dal
    selettore foto e dalla fotocamera; l'HEIC arriva solo da «Scegli file»,
    macOS Safari e browser desktop. Togliere HEIC da `accept` non cambia nulla
    su iPhone e rischia di nascondere i `.heic` in Chrome desktop: non toccare.
  - **Link rotto in CAM-1.3 (Dev Notes, ultima fonte):**
    `../../../Modules/Fixcity/...` da `Media/docs/bmad/stories/` non esiste;
    il percorso giusto e' `../../../../Fixcity/docs/bmad/stories/mobile-camera-upload.story.md`.
- **17:50, sessione Claude 6d2caca8: esito della revisione di sicurezza
  indipendente (50 chiamate di tool, sola lettura).** Confidenza del revisore:
  A = letto nel codice, B = dedotto, C = non verificato. Verificati da me:
  l'header e' ancora `camera=()` (`config/security.php:115`) e un test lo fissa
  (`SecurityHeadersTest.php:37`); `CreateTicketAction` fa `Ticket::create`, poi
  evento e ricevuta, senza transazione. Rilievi con la story che li possiede:
  - **CAM-1.3, alta (A il flusso, B il guasto): lo strip EXIF puo' lasciare un
    ticket orfano.** `StripTicketPhotoMetadataListener` non ha `try/catch` e
    parte dentro Spatie `Filesystem::add`, dopo la copia sul disco `public` e
    dopo la riga media; il ticket esiste gia'. Un JPEG con firma valida ma corpo
    illeggibile fa lanciare `decodeBinary`: restano ticket, riga media e
    originale con GPS su `/storage/{id}/...`, e il ritentativo crea un duplicato.
    Correzione minima proposta: nel listener, su `Throwable`, cancellare il media
    (e il file) e rilanciare come errore di validazione; meglio ancora provare la
    decodifica in validazione, prima di `Ticket::create`. I test che lo provano
    sono in scrittura (agente di questa sessione): l'esito reale, rosso o verde,
    va qui sotto prima di toccare il codice. **Nota:** il revisore ipotizza anche
    un fatal per memoria sui JPEG enormi; la mia misura qui lo smentisce
    (picco PHP 28 MB a 50 MP, la libgd di sistema alloca fuori da
    `memory_limit`), quindi il tetto ai pixel resta non necessario in questo
    runtime. Il caso del file illeggibile e' un'eccezione e resta valido.
  - **CAM-1.1, alta se gli ospiti arrivano al wizard (A il codice, C l'accesso):
    l'upload temporaneo non controlla il contenuto.** `config/livewire.php`
    lascia `rules => null` (default `required|file|max:12288`) e throttle
    `60,1`: il file finisce in `livewire-tmp` prima di qualunque controllo sul
    MIME, con l'estensione scelta dal client; il controllo `mimetypes` di
    Filament legge i byte reali ma gira solo a «Avanti» o all'invio. Un file
    contraffatto non crea media ne' file pubblico (Spatie rifiuta prima), ma il
    temporaneo resta fino a 24 ore e a 60 richieste al minuto da 12-16 MB sono
    0,7-1 GB al minuto per IP. Proposta: `rules` con `mimetypes` e
    `max:10240`, throttle piu' stretto, pulizia oraria di `livewire-tmp`. Prima
    di farlo: la regola e' globale, controllare gli altri upload dell'app, e
    verificare se il wizard richiede il login.
  - **CAM-1.1 / infrastruttura, media: `camera=(self)` non e' ancora nel
    codice e, fatto globale, varrebbe anche per `/admin`.** La fonte e' un
    middleware globale con una sola config. Proposta: middleware di route solo
    sul wizard (`SecurityHeaders` non sovrascrive un header gia' presente,
    `:49`), `getUserMedia({video, audio:false})`, test che `/admin` e le altre
    pagine mantengano `camera=()` e `microphone=()`. Vale solo se si adotta
    `getUserMedia`; con la cattura nativa (decisione D1) l'header non cambia.
  - **CAM-1.1, media (B il deploy): `.user.ini` e `.htaccess`.** `.user.ini`
    vale solo con FPM/CGI sul docroot `public_html`; ignorato da mod_php e da
    `php artisan serve`. La regola `<Files ".user.ini"> Require all denied`
    richiede `AllowOverride AuthConfig`: se l'hosting concede solo
    `FileInfo`/`Options`, Apache risponde 500 su tutta la cartella, rewrite
    inclusi. Su nginx `.htaccess` e' ignorato e il file e' leggibile via HTTP
    salvo regola sui dotfile. Contiene solo i due limiti: nessun segreto. Il file
    e' ancora non tracciato. Da verificare sul server vero (fixcity.eu).
  - **Privacy, bassa/media (A): l'originale con GPS arriva al server.**
    `prepare()` restituisce l'originale se e' JPEG/PNG sotto 900 KB e 2048 px, e
    un PDF non viene mai ripulito; lo strip server copre solo i media di
    `Ticket` che passano da Spatie. Il temporaneo con EXIF resta su disco
    privato fino a 24 ore. L'anteprima del riepilogo usa un URL firmato di 30
    minuti non legato alla sessione e servito con `Cache-Control: public,
    max-age=31536000`: una cache condivisa potrebbe tenere la foto con GPS per
    un anno. Opzionale: `Cache-Control: private, no-store` sulla route
    `livewire.preview-file`. Collegata alla story privacy in `review`.
  - **Altri (bassa/media, B):** la restrizione degli upload ai soli campi dello
    schema e' opt-in e non copre le pagine Resource del back office; i media
    stanno su disco `public` con nome casuale (nessun IDOR), ma i PDF non sono
    ripuliti e sono pubblici per chi ha il link: decisione di prodotto.
  - **Non verificato dal revisore:** web server, `AllowOverride`, nginx e
    `memory_limit` di produzione; se il wizard richiede il login; il
    comportamento reale di GD su JPEG troncati; `APP_ENV=local`/`APP_DEBUG=true`
    nel `.env` locale.
- **Sessione Claude a9bceaa6: stessa conclusione di 6d2caca8 (nessuna story nuova), con le prove che mancavano.**
  - Le cinque bozze `1.x` sono `superseded` con `superseded_by` dalle 17:45:20: il «Problema aperto» sulle due serie e' chiuso. Il
    link rotto di CAM-1.3 e' gia' corretto. I residui operativi hanno le loro story: `dcp-417` (limiti nginx/FPM di produzione),
    `dcp-418` (dispositivi reali), `dcp-419` (testo del limite), `dcp-420` (fixture con limiti predefiniti), `dcp-424` (accessibilita').
  - **File contraffatto, misurato fino in fondo** (Dev Agent Record di CAM-1.3): `foto.jpg` da 4 KB che inizia con
    `<?php system(...)`, inviata a `$wire.upload()` saltando il client, server con limiti PHP predefiniti. Il temporary upload
    risponde 200 e il file resta in `livewire-tmp` come `.jpg` (3 file dopo 3 prove): conferma il rilievo di 6d2caca8 su CAM-1.1.
    Il rifiuto arriva a «Avanti» con `mimetypes`, il passo non avanza e nessun media nasce. Qui l'utente leggeva la chiave grezza
    `validation.mimetypes` perche' mancava in `Xot/lang/it`: ora c'e' (e in de, es), test 111 passati. La regola `rules` di Livewire
    non e' toccata: e' globale e la decide CAM-1.1.
  - D2, dato nuovo: su questo PC `ext-imagick` manca, `libheif.so.1` c'e', `php8.4-imagick` 3.8.1 e' installabile ma non installato,
    quindi `Imagick::queryFormats('HEIC')` non e' eseguibile. Raccomandazione invariata: riduzione nel browser, Imagick al piu' come
    fallback dopo il probe su fixcity.eu.
  - Claim: `Modules/Xot/lang/{it,de,es}/validation.php`, `Modules/Xot/tests/Unit/ValidationMessagesTest.php`,
    `Modules/Media/app/Actions/Upload/GetEffectiveUploadLimitAction.php` con il suo test e, in `dc-file-upload.blade.php`, solo il
    calcolo di `$maxBytes` (riga 28). Non tocco `camera-upload.js` ne' le chiavi `ticket_form`.
  - Prossimo passo mio: sostituire quel calcolo con l'Action, dopo aver riletto la Blade (ultima modifica 17:39) e rieseguito il test
    del wizard su DB di prova.
- **17:52, sessione Claude 6d2caca8: esito dei test sul contenuto dei file (agente di prova, riverificato da me).**
  - **Prove aggiunte, nessun codice di produzione toccato:**
    `Modules/Fixcity/tests/Feature/Filament/CreateTicketWizardForgedUploadTest.php` (19 casi) e
    `Modules/Media/tests/Unit/Actions/Image/StripImageMetadataActionTest.php` (4 casi, gruppo `media-db`).
  - **Riesecuzione mia, un solo Pest alla volta, SQLite di prova, DB di sviluppo contato prima e dopo (tickets 538, media 72, users 85,
    invariati):** `StripImageMetadataActionTest` 4 passati (33 asserzioni); `CreateTicketWizardForgedUploadTest` **14 passati, 5 falliti**
    (95 asserzioni). I 5 rossi sono voluti: documentano un difetto, non vanno corretti ammorbidendo il test.
  - **Regge:** il controllo e' sul contenuto, non sul nome ne' sul MIME dichiarato. PHP travestito da `foto.jpg` (dichiarato
    `image/jpeg`) e HTML travestito da `foto.png` sono respinti; un JPEG vero con MIME `null`, vuoto o `application/octet-stream` e'
    accettato; un JPEG vero chiamato `foto.txt` e' accettato e salvato come `image/jpeg` con `file_name` `foto.txt` (se normalizzare
    l'estensione e' una scelta di prodotto, non un difetto). `StripImageMetadataAction` toglie il GPS dai JPEG (e applica la
    rotazione), toglie i chunk di testo dai PNG, lascia i PDF identici byte per byte; decide in base al MIME del record, non ai byte.
  - **Difetto confermato (A, riprodotto): un JPEG con intestazione valida e corpo illeggibile lascia un ticket orfano.** Casi: JPEG
    tagliato nell'intestazione JFIF e JPEG con Exif+GPS senza dati immagine. `Ticket::create` e' gia' avvenuto quando
    `StripTicketPhotoMetadataListener` (nessun `try/catch`, registrato su `MediaHasBeenAddedEvent`) chiama `decodeBinary`, che lancia
    `Intervention\Image\Exceptions\ImageDecoderException`. Effetti misurati: ticket 2 -> 3, media 1 -> 2, file 0 -> 1; il cittadino
    riceve un 500; **l'originale con il GPS resta sul disco pubblico**; un nuovo invio crea un doppione.
    `CreateTicketAction` non ha `DB::transaction`. I JPEG troncati ma decodificabili passano e sono salvati ricodificati.
  - **Corretto un mio errore di metodo:** una prima esecuzione ha dato 6 rossi invece di 5 perche' ho lanciato Pest mentre un'altra
    sessione ne aveva uno attivo sullo stesso file (il disco `public` finto sta in una cartella condivisa: un suo file `11/....jpg`
    ha sporcato l'asserzione «nessun file sul disco»). Rieseguito da solo: 5 rossi. Regola: `pgrep -af 'vendor/bin/pest'` e,
    se c'e' un processo, non partire.
  - **Correzione proposta (non applicata, da concordare con chi ha CAM-1.3 e CAM-1.1):** (1) in `StripTicketPhotoMetadataListener`
    `try/catch Throwable`: elimina il media (e quindi il file) e rilancia come `ValidationException` sul campo allegati con una chiave
    `__()` it/en/de/es; (2) meglio ancora, verificare la decodificabilita' prima di `Ticket::create`; (3) racchiudere la creazione del
    ticket in `DB::transaction` cosi' un guasto del listener annulla anche la riga. Il file `StripImageMetadataAction` non cambia.
    I 5 test rossi diventano il criterio di accettazione. Collegato: CAM-1.3 (contratto server), CAM-1.5 (gate di rilascio).
  - **Nota sulla copertura:** `Illuminate\Http\Testing\File` dichiara il MIME e non guarda i byte; i test esistenti con file finti
    (`CreateTicketWizardInputValidationTest`, `TicketFormImagesUploadTypesTest`) non possono provare nulla sul contenuto. I nuovi usano
    `UploadedFile` veri con byte veri (caso di controllo incluso).

- **2026-10-27 20:58, Claude (a9bceaa6), dopo le ondate di marker `.merge_file_`**
  - **Stato dell'albero.** Le ondate sono arrivate ogni circa 4 minuti (20:43:46, 20:47:47, 20:51:52). Dopo quella delle 20:51:52 i file del
    campo upload (`dc-file-upload.blade.php`, `lang/*/ticket_form.php`, `TicketForm.php`, `CreateTicketWizardDataStepTest.php`,
    `Themes/Sixteen/resources/js/app.js`) sono rimasti sul **lato B**: client Media `MediaCameraImage`, pulsanti «Scatta una foto» e
    «Scegli file». Il lato A (client del tema `FixcityCameraUpload`, peso obiettivo 900 KiB, galleria separata) non e' piu' nell'albero e
    resta solo in HEAD di Fixcity e Sixteen. Prendo B come stato corrente: sta nel modulo Media, come chiedeva l'utente.
  - **Cosa si era perso e l'ho rifatto.** Il tetto in byte passato al client. Il test «caps the upload at the Livewire temporary upload
    rule» era sopravvissuto e dava rosso (2.097.152 al posto di 102.400): la Blade di B calcolava ancora la formula copiata. Ora legge
    `GetEffectiveUploadLimitAction` (Media); tolti `$clientMaxKb` e una chiave `prepare` duplicata nell'array dei testi.
    `WizardUploadHelpTextTest` aveva scritti a mano `maxSide: 1920` (la costante `CameraPhotoUpload::MAX_SIDE` vale 2048) e
    `min(10 MB, PHP)`: ora legge la costante e l'Action. Rosso prima, 20 test verdi dopo (wizard 8, aiuto 5, Action 7); PHPStan e Pint
    puliti sui file toccati.
  - **Revisione del client B (non modificato, e' di Media).** Una sola passata: lato lungo 2048 px, qualita' 0,85, file sotto 1 MB inviati
    com'erano. Manca il ciclo di qualita' verso un peso obiettivo che aveva A (900 KiB). Con PHP a 2 MB funziona; con nginx a 1 MB
    (DCP-417, non ancora letto in produzione) una foto molto dettagliata da circa 1,3 MB darebbe di nuovo 413. Raccomandazione a Media:
    dopo la riduzione, se `file.size` supera il tetto, riprovare con qualita' 0,75 / 0,65 / 0,55 e poi con il lato piu' corto, e provarlo
    con la fixture a limiti predefiniti (DCP-420). Aperto: `ticket_form.php` ha ancora due chiavi `error_prepare` (la seconda vince).
  - **Foto nel dettaglio del ticket.** Non rifatta qui. Vince DCP-379: il ticket 2 e' `pending`, quindi la galleria pubblica di CAM-1.6 non
    lo mostrerebbe al proprietario, e `APP_URL=http://fixcity.local` in produzione rompe `getFullUrl()`. Claim della sessione 6d2caca8 sui
    sei file della funzione fino alle 21:15.
  - **PHPStan `analyse Modules` (ordine permanente).** Tre giri. 20:50 e 20:58: exit 1, solo parse error in
    `BuildPublicTicketDetailActionTest.php` (marker) e «Result is incomplete because of severe errors»: non misurabile. 21:01-21:02, albero
    senza marker PHP: **exit 0, nessuna segnalazione** (`phpstan analyse Modules --no-progress`). Nessun fan-out necessario.
    Test del dettaglio pubblico della sessione 6d2caca8 letti a sola lettura alle 21:02: `BuildPublicTicketDetailActionTest` e
    `TicketPublicDetailPageTest`, 35 passati (306 asserzioni), compreso «gives the photos of a report not yet public to the owner only».

## Dev Agent Record

### 2026-10-27, Claude (sessione parallela a quella che ha scritto questa story)

**Studio delle fonti.** Letti: discussione Filament 11974, repo e note di `filament-take-picture-field`, pagina e repo di
`spykapp/uppy-upload-plugin`. Non raggiungibili da qui: AnswerOverflow (429) e i tre articoli Medium (403), quindi non sono
evidenza. Sintesi utile: Take Picture Field usa `getUserMedia` con `imageQuality` e `captureMaxDimensions` e richiede HTTPS;
Uppy spezza in chunk da 5 MB per superare proxy e nginx ma aggiunge endpoint, autorizzazioni e cleanup. Nessuna delle due tocca
il limite PHP/nginx ne' HEIC. Scelta coerente con le Dev Notes: input nativo + riduzione nel browser, niente secondo uploader.

**Causa riprodotta (limiti PHP predefiniti 2M/8M).** Foto 2,4 MB: 422 con la chiave grezza `validation.uploaded`. Foto 9 MB: 413
`PostTooLargeException`, e Livewire mostra la sua frase inglese fissa «The image failed to upload». Il fixture :8091 parte con
12M/16M e nasconde il difetto: la prova vale solo su un server con i limiti predefiniti (:8096, avviato per questo).

**Modifiche.**
- `Themes/Sixteen/resources/js/components/camera-upload.js`: `prepare(file, {maxBytes, maxEdge})`. PDF intatto; HEIC riconosciuto
  dai byte `ftyp` (non dall'estensione); decodifica con `createImageBitmap(..., {imageOrientation: 'from-image'})`; lato lungo 2048;
  tetto 16 Mpx del canvas iOS; JPEG con sfondo bianco, qualita' 0,85 a 0,55, fino a 3 riduzioni del 25%; rifiuta se non scende
  sotto il limite. Nessun testo visibile nel JS.
- `Modules/Fixcity/resources/views/filament/forms/components/dc-file-upload.blade.php`: tetto = min(limite campo, 90% del limite
  PHP reale), e per la riduzione min(tetto, 900 KiB) (1 MiB e' il limite predefinito di nginx e il multipart ha overhead);
  `normalizeCameraImage` chiama `prepare` prima di `$wire.upload()`; riga in stato `preparing`; input fotocamera con `capture` e
  `accept` solo immagini (con il PDF nell'elenco i telefoni aprono il selettore generico); secondo input senza `capture`
  (galleria, con il PDF) perche' `capture` toglie la scelta della galleria.
- `Modules/Fixcity/lang/{it,en,de,es}/ticket_form.php`: `gallery_button`, `gallery_aria`, `preparing`, `error_prepare`.
- `validation.uploaded` come frase intera, senza `:attribute` (Livewire passa un nome interno), in it (Xot), de e es (User) e in
  `laravel/lang/en/validation.php` (nuovo, solo questa chiave). `Xot/lang/de/validation.php`: `distinct` era inglese, tradotto.
- Test: `Fixcity/tests/Unit/TicketFormImagesLangTest.php` (nuovo), `Fixcity/tests/Feature/Filament/CreateTicketWizardDataStepTest.php`,
  `Xot/tests/Unit/ValidationMessagesTest.php`.

**Verifica reale.**
- Pest su DB di prova isolati (`bashscripts/tools/pest-scratch-db.sh`): primo giro 129 passati e 1 fallito (`distinct` tedesco
  ancora in inglese); dopo la correzione il file Xot da solo: 108 passati; test di upload di Fixcity: 22 passati (268 asserzioni).
  Ultimo giro, dopo aver stretto i test linguistici con `app('translator')->hasForLocale(...)` (con `fallback_locale = it`
  `trans()` da solo non scopre una chiave mancante in de ed es): `TicketFormImagesLangTest` + `ValidationMessagesTest`, 119
  passati (362 asserzioni). Il giro con `:memory:` (vedi Incidente) non vale.
- ESLint: 0 errori (2 avvisi preesistenti in `app.js:242`). `npm run build` del tema: ok; `prepare` presente nel bundle, chunk
  `vendor-image-conversion` separato, nessun file a 0 byte.
- curl sul server con limiti predefiniti: 0,6 MB 200; 2,4 MB 422 con la frase italiana intera; 9 MB 413 (resta il testo
  inglese del framework: si raggiunge solo saltando il browser).
- Browser a 390 px, server con limiti predefiniti, utente loggato, wizard passo dati (misure dal file arrivato sul server):
  | Ingresso | Arrivato |
  | --- | --- |
  | JPEG 4032x3024, 6,2 MiB | JPEG 2048x1536, 713 KiB, 200 |
  | JPEG rumore casuale 8,65 MB (caso peggiore) | JPEG 2048x1536, 904 KiB, 200 |
  | HEIC vero 8,1 MB (`ftypheic`, creato con pillow-heif in un venv isolato) | `IMG_0001.jpg`, JPEG 2048x1536, 838 KiB, 200 |
  | PDF 3 MB | rifiutato nel browser: «Il file supera 1.8 MB», nessuna richiesta al server |
  Le tre foto compaiono nell'elenco con anteprima e pulsante di rimozione, senza errori.

**Non verificato (il fix NON e' chiuso).**
- Dispositivi reali: iPhone Safari HTTPS, Android Chrome, Samsung Internet. Chromium headless non copia il comportamento di
  `capture`, ne' il limite canvas di Safari.
- Misurato in browser: viewport 375, 390, 768 e 1440 px senza overflow orizzontale, pulsanti a tutta larghezza alti 48 e 50 px;
  pulsanti, `aria-label` e suggerimento letti nel DOM in it, en, de, es (tutti tradotti). NON e' una prova di accessibilita':
  focus, tastiera, live region e lettore di schermo non provati ([DCP-424](../../stories/dcp-424-accessibilita-del-campo-allegati-da-fotocamera.story.md)).
  Il messaggio `error_prepare` non l'ho provocato in browser (coperto solo dal test sulle chiavi).
- Produzione fixcity.eu (altro server, 46.225.90.178): il limite nginx `client_max_body_size` non e' stato letto; `.user.ini` non
  agisce su `php -S` ne' su nginx.
- AC server (MIME reale, cleanup, rollback, ownership): vedi Agent review qui sotto; non sono stati provati da questa sessione.

**Aperto.** Il testo del campo dice «massimo 1.8 MB ciascuno» (limite PHP reale) anche per le foto, che invece vengono ridotte da
sole: vero per i PDF, fuorviante per le foto. Tracciato in [DCP-419](../../stories/dcp-419-testo-del-limite-allegati-distingue-foto-e-pdf.story.md).

**Story di seguito (create il 2026-10-27, nessun lavoro avviato).** Quelle di altre sessioni, che coprono questa story:
[DCP-417](../../stories/dcp-417-limiti-effettivi-php-e-nginx-di-fixcity-eu-per-gli-allegati.story.md) (limiti di produzione, `.user.ini`),
[DCP-418](../../stories/dcp-418-matrice-dispositivi-reali-per-upload-da-fotocamera.story.md) (dispositivi reali),
[DCP-420](../../stories/dcp-420-fixture-di-prova-con-limiti-php-predefiniti.story.md) (fixture con limiti predefiniti),
[DCP-424](../../stories/dcp-424-accessibilita-del-campo-allegati-da-fotocamera.story.md) (accessibilita'),
[DCP-428](../../../../Media/docs/stories/dcp-428-upload-temporaneo-proprieta-mime-pulizia-e-rollback.story.md) e
[DCP-429](../../../../Media/docs/stories/dcp-429-conversione-heic-lato-server-decisione-su-imagick.story.md) (server Media, HEIC e Imagick).
Quelle che ho scritto io per i rilievi rimasti scoperti:
[DCP-435](../../../../Xot/docs/stories/dcp-435-guardia-test-accetta-memory-con-driver-mysql.story.md) (guardia dei test e incidente `:memory:`),
[DCP-436](../../../../Lang/docs/stories/dcp-436-traduttore-registra-le-chiavi-dinamiche-di-validation-attributes.story.md) (registrazione chiavi nel traduttore),
[DCP-437](../../../../Xot/docs/stories/dcp-437-cataloghi-validation-messaggi-inglesi-e-chiavi-uploaded-in-conflitto.story.md) (cataloghi di validazione),
[DCP-438](../../stories/dcp-438-campo-allegati-rimozione-fallita-e-avanti-durante-il-caricamento.story.md) (rimozione fallita e Avanti durante il caricamento).

## Story map BMAD

Questa è la story coordinatrice cross-module. Le attività sono suddivise nelle
seguenti stories, che contengono ownership, criteri e handoff specifici:

- [CAM-1.1 Contratto runtime trasporto](CAM-1.1-runtime-transport-contract.story.md)
- [CAM-1.2 Normalizzazione browser/Livewire](../../../../../Themes/Sixteen/docs/bmad/stories/CAM-1.2-browser-normalization-livewire.story.md)
- [CAM-1.3 Contratto Media e Imagick](../../../../Media/docs/bmad/stories/CAM-1.3-server-media-contract-imagick.story.md)
- [CAM-1.4 Traduzioni e accessibilità](CAM-1.4-translations-accessibility.story.md)
- [CAM-1.5 Gate verifica e rilascio](CAM-1.5-verification-release-gate.story.md)

**Incidente di processo.** Una mia esecuzione di Pest con `DB_DATABASE=:memory:` ha creato nel MariaDB di sviluppo uno schema
chiamato `:memory:` (76 tabelle) e 108 test sono falliti. Ho eseguito `DROP DATABASE` su quello schema e ho verificato che i
database veri sono intatti (`fixcity_data` 94 tabelle, `fixcity_user` 39; categories 25, tickets 538, users 85). Da qui in poi
solo `pest-scratch-db.sh`.

**Agent review (2026-10-27).** Due revisori indipendenti (client JS/Blade; lato server e traduzioni) hanno letto il codice senza le
mie conclusioni. Ho riletto nel codice ogni rilievo prima di agire; un rilievo non e' una prova.

Client, corretti: la decodifica del browser si prova prima di `heic2any` (che serve solo dove il decoder fallisce); un PDF con tipo
vuoto si riconosce dall'estensione; l'input fotocamera accetta solo immagini; `role="alert"` sul messaggio d'errore; `aria-label`
che inizia con il testo visibile (galleria e, in tedesco, carica); canvas rilasciato e `imageSmoothingQuality = 'high'`; try/catch
morto tolto; tetto di riduzione 900 KiB al posto di 1 MiB; registro formale del tedesco.
Client, non corretti e tracciati: rimozione fallita in silenzio e Avanti durante il caricamento
([DCP-438](../../stories/dcp-438-campo-allegati-rimozione-fallita-e-avanti-durante-il-caricamento.story.md); il revisore non
aveva letto il wizard Filament, quindi il secondo e' da riprodurre); focus, tab stop degli input nascosti e live region (DCP-424);
testo «1.8 MB» per le foto (DCP-419). Minori, non tracciati: `upload_aria` in it/en/es invariato, separatore decimale «1.8» in
tutte le lingue, un solo slot `message` condiviso, rimozione durante `preparing` che mostra un errore.

Server e traduzioni, positivo (verificato dal revisore): il MIME reale e' validato dal contenuto (`mimetypes` da `acceptedFileTypes`,
Livewire legge 64 KB con finfo, il Ticket ha un secondo livello); lo stadio temporaneo non controlla il MIME e non va aggiunto li';
`laravel/lang/en/validation.php`, con la sola chiave `uploaded`, non oscura le regole inglesi del framework.
Corretto: i miei test linguistici provavano meno di quanto dicessero (con `fallback_locale = it` un `trans()` torna in italiano);
ora usano `hasForLocale`. Premessa corretta: la frase inglese «failed to upload» e' costruita in PHP da Livewire, non nel JS.
Aperti: `.user.ini` non agisce su `php -S` (confermato) e il limite di produzione non e' leggibile da qui (DCP-417, DCP-420);
`public_html/.user.ini` e `laravel/lang/en/validation.php` non sono tracciati da git (nessun commit chiesto, decide l'utente);
registrazione delle chiavi dinamiche ([DCP-436](../../../../Lang/docs/stories/dcp-436-traduttore-registra-le-chiavi-dinamiche-di-validation-attributes.story.md));
cataloghi con chiavi in conflitto e messaggi inglesi
([DCP-437](../../../../Xot/docs/stories/dcp-437-cataloghi-validation-messaggi-inglesi-e-chiavi-uploaded-in-conflitto.story.md)).
