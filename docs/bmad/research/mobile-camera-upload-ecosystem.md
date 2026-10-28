---
title: "Ricerca tecnica: upload da fotocamera mobile in Filament/Livewire"
type: research
status: completed
module: Fixcity
related_module: Media
updated: 2026-10-27
---

# Sintesi

Il problema non è un limite di Filament alla fotocamera. Il flusso attuale usa
un normale `input[type=file]` con `capture="environment"`, poi `$wire.upload()`.
La prova locale ha mostrato che una foto di 4,4 MB veniva respinta prima di
Laravel con `422 validation.uploaded` quando PHP aveva `upload_max_filesize=2M`.

La soluzione più piccola e affidabile per il front office è mantenere il campo
Livewire/Spatie già integrato e aggiungere un percorso camera nativo:
`getUserMedia()` → `<video playsinline muted>` → canvas JPEG ridimensionato →
`$wire.upload()`. Il picker file rimane per galleria, PDF e browser senza camera.

## Fonti esaminate

| Fonte | Evidenza utile | Decisione |
|---|---|---|
| Filament discussion #11974 | `extraInputAttributes(['capture' => 'environment'])` risolve il percorso mobile nativo; il desktop richiede un campo webcam distinto | mantenere il picker e aggiungere camera esplicita |
| `emmanpbarrameda/filament-take-picture-field` | usa `getUserMedia`, `playsinline`, `muted`, gestione permessi/HTTPS, canvas JPEG, dimensioni massime e qualità configurabili; traduce le chiavi dal server | riusare il pattern, non la dipendenza: il suo stato base64/persistenza non coincide con `TemporaryUploadedFile` + Media Library |
| Dev Notes Take Picture | propone aspect ratio, `captureMaxDimensions(1280,720)` e qualità JPEG configurabile | adottare 1280 px come tetto camera e qualità configurabile dal contratto |
| SpykApp Uppy | webcam, image editor e chunk da 5 MB; upload diretto a endpoint/disk, 11 lingue | non adottare: introduce un secondo contratto di storage e un endpoint che bypassa lo stato Filament |
| Livewire uploads | upload temporaneo predefinito `file|max:12288` (12 MB) | mantenere 10 MB applicativi, PHP 12/16 MB con headroom |
| MDN `capture` | `capture="environment"` è un suggerimento per il file input, non un accesso camera universale | non considerarlo sufficiente come unica UX |

## Alternative scartate

1. **Solo `capture`**: già presente; non corregge PHP, HEIC, desktop e browser
   che trattano `capture` come semplice suggerimento.
2. **Take Picture Field come dipendenza**: ottimo riferimento per webcam, ma
   salva base64/path in un campo proprietario e richiede integrazione diversa
   dalla collezione `attachments` del ticket.
3. **Uppy**: chunking utile oltre i limiti proxy, ma sproporzionato per foto da
   10 MB e rischioso perché sposterebbe la persistenza fuori da Livewire e
   `saveRelationships()`.
4. **Compressione server dopo l'upload**: arriva troppo tardi se PHP/Nginx
   rifiutano il multipart; la riduzione deve avvenire prima del trasporto.

## Contratto multilingua e sicurezza

Nessuna frase viene scritta nel JavaScript o nella view. Il server passa tutte
le etichette camera da `fixcity::ticket_form.fields.images.*`; il modulo deve
continuare a fornire `it`, `en`, `de`, `es`. Il server mantiene la whitelist
JPEG/PNG/PDF e la validazione 10 MB. La camera produce soltanto JPEG, non
allarga la Media Library a HEIC.

## Fonti web

- https://github.com/filamentphp/filament/discussions/11974
- https://github.com/emmanpbarrameda/filament-take-picture-field
- https://emmanpbarrameda.github.io/dev-notes/filament-take-picture-field/
- https://github.com/SpykApp/uppy-upload-plugin
- https://livewire.laravel.com/docs/uploads
- https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Attributes/capture
