---
title: "DCP-424: accessibilita del campo allegati da fotocamera"
type: story
status: ready-for-dev
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, accessibilita, wcag, upload, fotocamera, aria]
qmd: "accessibilita campo allegati fotocamera focus tastiera live region aria-label screen reader dc-file-upload"
related:
  - ../bmad/stories/mobile-camera-upload.story.md
  - ./dcp-418-matrice-dispositivi-reali-per-upload-da-fotocamera.story.md
issues: []
discussions: []
---

# DCP-424: accessibilita del campo allegati

## Origine

La story madre ([mobile-camera-upload](../bmad/stories/mobile-camera-upload.story.md)) dichiara non provati focus, tastiera e live region. Misurato finora: nessun overflow orizzontale a 375, 390, 768 e 1440 px; i due pulsanti a tutta larghezza alti 48 e 50 px; `aria-label` e suggerimento letti nel DOM in it, en, de, es. Non e' una prova di accessibilita'.

## Acceptance criteria

1. Tutti i controlli del campo (scatta, galleria, rimuovi) si raggiungono e si attivano con la sola tastiera, con ordine del focus logico e focus visibile.
2. Gli stati cambiano senza che il lettore di schermo li perda: preparazione, caricamento con avanzamento, successo, errore, rimozione. Ogni stato e' annunciato in una live region (`role="status"` per gli esiti, `role="alert"` per gli errori), con testi da chiavi di lingua.
3. Ogni pulsante ha un nome accessibile nelle quattro lingue; le anteprime hanno un testo alternativo sensato, non il nome del file.
4. Il contrasto di testi e bordi rispetta WCAG AA nei tre stati (normale, focus, errore).
5. Un controllo automatico (se uno strumento e' gia' presente nel repo; altrimenti verifica manuale documentata) e una prova con un lettore di schermo reale: quale e dove vanno scritti. Se non e' possibile provare VoiceOver o TalkBack, la story lo dichiara invece di dare la prova per fatta.

## Tasks

- [ ] Rileggere `dc-file-upload.blade.php` (modificato dall'altra sessione) e annotare gli attributi ARIA presenti.
- [ ] Verifica da tastiera in Chromium con le quattro lingue; elenco dei difetti.
- [ ] Correzioni nel markup e nelle chiavi `fields.images.*`.
- [ ] Prova con lettore di schermo (con [DCP-418](./dcp-418-matrice-dispositivi-reali-per-upload-da-fotocamera.story.md) per i telefoni).
- [ ] Collegarsi all'audit DCP-143 per non duplicare i controlli gia' fatti sul wizard.

## Owned file scope

`laravel/Modules/Fixcity/resources/views/filament/forms/components/dc-file-upload.blade.php`, lang del modulo; il JS (`Themes/Sixteen/resources/js/components/camera-upload.js`) solo se serve, senza stringhe visibili.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato |
