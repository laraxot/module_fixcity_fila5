---
name: wizard-visual-parity
description: Regola di parità visuale per il wizard Segnalazione in Fixcity
type: concept
---

# Wizard Visual Parity – Segnalazione

## Contesto
Il wizard `/it/tests/segnalazione-crea` deve rispecchiare la pagina di riferimento **Design Comuni** *segnalazione‑03‑riepilogo*.

## Elementi mancanti (prima della correzione)
- **Submit button** assente nello step `form.riepilogo`.
- **Header alignment** non conforme allo slim‑bar di Design Comuni.
- **Mappa invisibile** al passaggio al passo successivo a causa di CSS specifici per pagina (`.segnalazione-wizard-root`).

## Correzioni applicate
1. Aggiunto bottone Submit generico: `<x-button type="submit">Invia segnalazione</x-button>`.
2. Allineato header usando gli stili globali di `header-footer-colors.css`.
3. Rimosso CSS page‑specific dal file `resources/css/app.css` (vedi regola *No Page‑Specific CSS*).
4. Aggiunta regola globale in `filament-wizard-parity.css` per garantire la visibilità della mappa:
   ```css
   .filament-wizard coordinate-picker-lit { display:block !important; width:100% !important; min-height:340px !important; }
   ```
5. Ricostruiti asset con `npm run build && npm run copy`.

## Perché è importante
- **DRY/KISS**: evita CSS sparsi per pagina.
- **Accessibilità**: garantisce la presenza del bottone Submit.
- **Coerenza UI**: conformità al Design System italiano.

---
