---
title: "DCP-418: matrice dispositivi reali per l'upload da fotocamera"
type: story
status: blocked
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, upload, fotocamera, ios, android, heic, test-manuale]
qmd: "dispositivi reali iphone safari android chrome samsung internet capture heic canvas upload fotocamera matrice"
related:
  - ../bmad/stories/mobile-camera-upload.story.md
  - ./dcp-424-accessibilita-del-campo-allegati-da-fotocamera.story.md
issues: []
discussions: []
---

# DCP-418: matrice dispositivi reali

Bloccata: servono dispositivi fisici e un indirizzo HTTPS raggiungibile da questi.

## Origine

Story madre: [mobile-camera-upload](../bmad/stories/mobile-camera-upload.story.md). La verifica fatta finora e' in Chromium a 390 px con file creati a mano (JPEG 4032x3024, HEIC vero 8,1 MB, PDF). Chromium headless non riproduce l'attributo `capture` ne' il limite del canvas di Safari: la story madre dichiara «il fix NON e' chiuso» per questo.

## Acceptance criteria

1. Una tabella con righe iPhone Safari, Android Chrome, Samsung Internet (versione di sistema e del browser indicate) e colonne: scatto con `capture`, scelta da galleria, HEIC (iPhone con formato «Alta efficienza»), JPEG, PDF.
2. Per ogni cella: esito (anteprima visibile, nessun errore), dimensione e dimensioni in pixel del file arrivato al server, orientamento corretto, tempo di preparazione percepito.
3. Una foto sopra il limite del canvas iOS (circa 16,7 Mpx, la guardia del codice e' 16 Mpx) e una molto grande (48 o 108 MP, se il telefono lo permette) mostrano esito e messaggio.
4. Il messaggio `error_prepare` e' provocato almeno una volta in un browser reale e letto nelle quattro lingue.
5. I casi falliti diventano difetti con riga in questa story, non note sparse.

## Tasks

- [ ] Predisporre HTTPS raggiungibile dai telefoni (il proxy di sviluppo `192.168.1.40:8080` espone TLS e HTTP; il certificato va accettato sul telefono) o usare l'ambiente pubblico.
- [ ] Preparare account di prova e istruzioni brevi per chi ha i dispositivi.
- [ ] Eseguire la matrice e compilare la tabella.
- [ ] Aprire difetti per le righe fallite e collegarli alla story madre.

## Verifica richiesta

Misure dal file arrivato sul server, non dal browser. Screenshot del dispositivo per i messaggi di errore.

## Owned file scope

Solo docs. Eventuali correzioni al codice vanno in story separate, perche' toccano `Themes/Sixteen/resources/js/components/camera-upload.js` e `dc-file-upload.blade.php`.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato |
