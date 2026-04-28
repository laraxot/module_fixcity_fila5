---
name: map-fullscreen-issues
description: Problemi di visualizzazione della mappa in fullscreen nel wizard Segnalazione
type: concept
---

# Problemi di Mappa in Fullscreen – Wizard Segnalazione

## Contesto
Nel wizard **Segnalazione** (`/it/tests/segnalazione-crea?step=form.dati-della-segnalazione::data::wizard-step`) la mappa presenta i seguenti comportamenti anomali quando si attiva la modalità fullscreen:

1. **Scrollbar verticale** appare sopra la mappa, impedendo la visualizzazione completa del contenuto.
2. Un **box informativo** (information request) si sovrappone alla mappa, bloccandone l’interazione.
3. La mappa **non si carica correttamente** (tiles mancanti, visualizzazione parziale).

## Cause note
- Integrazione Leaflet con il componente Lit `coordinate-picker-lit` non gestisce correttamente il ridimensionamento del container quando il wrapper passa da `hidden` a `fullscreen`.
- Stili CSS “overflow: auto” o “height: 100vh” applicati al wrapper della mappa generano la scrollbar.
- Il box informativo è inserito tramite un `<div class="info-box">` statico che non viene nascosto in modalità fullscreen.

## Correzioni proposte
1. **Rimuovere scrollbar**: assicurare che il container della mappa (`.map-wrapper`) abbia `overflow: hidden` e utilizzi `height: 100%`.
2. **Nascondere box informativo** in fullscreen mediante media query:
   ```css
   .map-fullscreen .info-box { display: none !important; }
   ```
3. **Trigger resize**: aggiungere un `ResizeObserver` o chiamare `map.invalidateSize()` quando il componente entra in modalità fullscreen (es. ascoltare l'evento `fullscreenchange`).
4. **Aggiornare CSS tema** in `filament-wizard-parity.css` per includere le regole sopra.

## Best practices
- Utilizzare **component‑level CSS** (no selector per pagina) per controllare lo stile della mappa.
- Gestire il cambio di stato fullscreen con **event listeners** nel file JS del lit component (`coordinate-picker-lit.js`).
- Verificare che il wrapper non erediti regole di overflow da stili globali.

---
