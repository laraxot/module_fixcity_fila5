---
story_id: CAM-1.1
epic: CAM-1
title: "Definire il contratto runtime del trasporto upload"
type: story
status: ready-for-dev
owner: Fixcity
priority: P1
---

# CAM-1.1: Definire il contratto runtime del trasporto upload

## Story

Come responsabile del wizard, voglio limiti coerenti tra browser, Livewire,
PHP e web-server, così una foto camera valida non viene rifiutata prima della
validazione applicativa.

## Acceptance Criteria (LOCKED)

1. Il limite applicativo per allegato resta 10 MB e quello di Livewire è
   esplicito e compatibile.
2. `upload_max_filesize`, `post_max_size`, FPM, web-server e proxy hanno
   headroom documentato sopra il limite applicativo.
3. Un file oltre limite e un request body oltre `post_max_size` producono un
   errore localizzato e non un ticket/media parziale.
4. I valori verificati distinguono configurazione versionata da runtime reale.

## Tasks / Subtasks

- [ ] Misurare i limiti effettivi nel runtime target e registrare la matrice.
  (AC: #2, #4)
- [ ] Impostare il contratto Livewire senza accettare MIME generici. (AC: #1)
- [ ] Verificare risposte 422/413 e mapping alle traduzioni. (AC: #3)
- [ ] Aggiornare solo la documentazione di deployment necessaria. (AC: #2, #4)

## Dev Notes (LOCKED)

- Livewire esegue il temporary upload prima di Media Library; Imagick non può
  intervenire se PHP o proxy rifiutano la richiesta. [Source: ../../../../Media/docs/bmad/mobile-camera-upload-investigation.md]
- La configurazione corrente usa regole temporary upload di default se non
  esplicitate. [Source: ../../../../../config/livewire.php]
- `.user.ini` non dimostra il valore attivo di FPM/nginx. [Source: ../../../../../../public_html/.user.ini]
- Il limite di sicurezza del campo non va ampliato a `image/*`. [Source: ./mobile-camera-upload.story.md]

## Testing

- **Approach:** test di configurazione + integrazione HTTP/Livewire.
- **Key scenarios:** JPEG sotto limite, JPEG sopra limite, body oltre limite,
  sessione scaduta.
- **Per-AC verification:** AC #1 config/assert; AC #2 runtime probe; AC #3
  response test; AC #4 report con valori effettivi.
- **Fixtures / mocks:** file JPEG deterministico da 0.5 MB e 10.1 MB; request
  body controllato; runtime PHP/FPM target.
- **Edge cases:** limite proxy inferiore, `post_max_size` inferiore a file,
  errore senza attributo Livewire leggibile.

## Dependency Maps

- **Blocked by:** none.
- **Blocks:** CAM-1.2, CAM-1.5.
- **External:** PHP/FPM, nginx/Apache, proxy, Livewire.

## Owned File/Module Scope

- `laravel/config/livewire.php`
- `public_html/.user.ini`
- `laravel/Modules/Fixcity/tests/Feature/Filament/CreateTicketWizardDataStepTest.php`
- `laravel/Modules/Fixcity/docs/bmad/`

## Learnings from Previous Stories

- L’errore può avvenire prima di Media Library; non attribuirlo alla sola
  conversione immagine. (from FIXCITY-MEDIA-001)

## Dev Agent Record

### 2026-10-27, Claude (sessione a9bceaa6): limite effettivo del trasporto

**Fatto e provato**

- `Modules/Media/app/Actions/Upload/GetEffectiveUploadLimitAction.php` (nuova, la possiede Media): restituisce il minimo tra il 90%
  del limite PHP letto dentro la richiesta web, la regola `max:` di Livewire e il limite del campo. Qui
  `config('livewire.temporary_file_upload.rules')` e' `null`, quindi Livewire applica `max:12288` KB. Test
  `Modules/Media/tests/Unit/Actions/Upload/GetEffectiveUploadLimitActionTest.php`: rosso (7 falliti), poi 7 passati; PHPStan 0,
  PHPMD senza segnalazioni.
- Causa riprodotta con i limiti PHP predefiniti (2M/8M, server :8095): file da 2 a 8 MB danno 422 `validation.uploaded`; oltre 8 MB
  danno 413. Il fixture :8091 parte con 12M/16M e nasconde il difetto: la prova vale solo con i limiti predefiniti.
- AC3: il 422 ora mostra la frase intera in it, de, es (vedi CAM-1.4). Il 413 mantiene il testo inglese fisso di Livewire e si
  raggiunge solo saltando il browser.

**Aperto**

- Chiuso il 2026-10-27 20:58: la Blade `dc-file-upload.blade.php` legge `GetEffectiveUploadLimitAction` (prima calcolava la formula
  inline e ignorava la regola Livewire). Dopo l'ondata di marker dell'albero la Blade era tornata alla formula copiata: il test «caps the
  upload at the Livewire temporary upload rule» e' diventato rosso (2.097.152 al posto di 102.400) e poi verde; `WizardUploadHelpTextTest`
  legge ora Action e `CameraPhotoUpload::MAX_SIDE`. Dettagli nel registro di `mobile-camera-upload.story.md`.
- AC2 e AC4: `client_max_body_size` di nginx e i valori FPM su fixcity.eu non sono stati letti; `.user.ini` agisce solo su FPM/CGI,
  non su `php -S`.
- AC1: una regola MIME in `config/livewire.php` varrebbe per ogni upload temporaneo dell'app (anche Filament admin): non applicata.
