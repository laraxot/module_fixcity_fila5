# header green branding rule

## Ambito (KISS)

Questa nota descrive **token verdi** usati nel perimetro Fixcity / test quando serve continuità con il **modello segnalazioni** del kit. **Non** sostituisce il riferimento visivo ufficiale del flusso HTML statico.

**Parità con [Design Comuni — segnalazione-02-dati](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-02-dati.html):** la fascia menu (`.it-header-navbar-wrapper`) nel wizard test è **chiara** (link scuri, hover/active verde), non una barra intera verde con testo bianco. Fonte di implementazione nel tema: [header color parity](../../../../../../Themes/Sixteen/docs/wiki/concepts/header-color-parity.md).

## Token (riferimento)

- `var(--dc-green)` / `#007A52` — accento, CTA, stati attivi dove coerente con il kit.
- `var(--dc-green-dark)` / `#00402B` — fascia slim nel flusso segnalazione, dove previsto.

## Cosa non fare

- Forzare `#007A52` come **sfondo unico** di tutta la `.navbar-nav` / `.navbar-secondary` per “parity”: è fuori dal prototipo statico e peggiora contrasto gerarchico.
- Duplicare regole CSS in Blade `<style>`: un solo posto nel tema (`app.css` + classe BI `theme-light-desk` sul wrapper).

## Collegamenti

- [visual parity report](./visual-parity-report.md)
- [segnalazione runtime asset integrity](./segnalazione-runtime-asset-integrity.md)
- [Wiki indice Fixcity](../index.md)
