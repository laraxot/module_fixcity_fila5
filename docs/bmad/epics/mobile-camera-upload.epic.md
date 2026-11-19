# Epic CAM-1 — Upload affidabile da fotocamera mobile

**Goal:** consentire al cittadino di allegare una foto scattata da smartphone
nel wizard della segnalazione, superando HEIC, dimensioni camera e failure del
temporary upload senza indebolire la validazione.

**Track:** Quick Flow cross-module

**In scope:**

- Cattura nativa e picker galleria nel campo `dc-file-upload`.
- Normalizzazione JPEG/PNG/HEIC prima di `$wire.upload()`.
- Contratto server Media per MIME, dimensione, ownership e cleanup.
- Traduzioni it/en/de/es, accessibilità e fallback.
- Limiti runtime PHP/web-server/proxy e gate browser.

**Out of scope:** Uppy/chunking e `getUserMedia` con modal dedicato; si valutano
solo dopo il fallimento dimostrato del percorso nativo.

**Architecture touchpoints:** Fixcity wizard e traduzioni, Sixteen camera
adapter, Media temporary upload/Spatie Media Library, PHP/FPM/web-server.

**Coordination story:**
[FIXCITY-MEDIA-001](../stories/mobile-camera-upload.story.md)

## Stories (ordered)

| ID | File | Intent | Status |
|---|---|---|---|
| CAM-1.1 | [runtime-transport-contract](../stories/CAM-1.1-runtime-transport-contract.story.md) | Rendere coerenti i limiti del trasporto | ready-for-dev |
| CAM-1.2 | [browser-normalization-livewire](../../../../../Themes/Sixteen/docs/bmad/stories/CAM-1.2-browser-normalization-livewire.story.md) | Collegare normalizzazione e upload reale | ready-for-dev |
| CAM-1.3 | [server-media-contract-imagick](../../../../Media/docs/bmad/stories/CAM-1.3-server-media-contract-imagick.story.md) | Difendere il confine Media e valutare Imagick | ready-for-dev |
| CAM-1.4 | [translations-accessibility](../stories/CAM-1.4-translations-accessibility.story.md) | Chiudere i messaggi e il contratto multilingua | ready-for-dev |
| CAM-1.5 | [verification-release-gate](../stories/CAM-1.5-verification-release-gate.story.md) | Provare il roundtrip e il rilascio | ready-for-dev |
| CAM-1.6 | [public-ticket-photo-gallery](../stories/CAM-1.6-public-ticket-photo-gallery.story.md) | Mostrare le foto sotto la mappa | ready-for-dev |

## Dependencies

- CAM-1.1 precede CAM-1.2: il normalizzatore deve conoscere il limite reale.
- CAM-1.2 e CAM-1.3 possono procedere in parallelo dopo il contratto.
- CAM-1.4 può procedere in parallelo; CAM-1.6 dipende dal contratto Media;
  CAM-1.5 dipende da tutte le precedenti.

## Delivery tracking

- Total stories: 6
- Done: 0
- Remaining: 6
- Completion rate: 0%

`docs/chat/` resta archivio storico e non è parte del backlog operativo.
