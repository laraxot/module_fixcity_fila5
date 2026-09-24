---
type: concept
created: 2026-05-04
tags: [design-comuni, visual-parity, comparison]
---

# Design Comuni Comparison: Segnalazione

Detailed comparison between the Fixcity Ticket Wizard and the [Design Comuni Segnalazione Disservizio](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html) standard.

## Overview
The comparison covers visual styling, HTML structure, and functional parity (privacy step, data entry, summary).

## Analysis & Plan
The master analysis and correction plan are owned by the **Sixteen Theme** to ensure no styling logic leaks into the module.

- [[../../../../Themes/Sixteen/docs/wiki/comparisons/segnalazione-01-privacy-parity]]
- [[../../../../Themes/Sixteen/docs/wiki/concepts/segnalazione-visual-parity-correction-plan]]

## Module Level Tasks
1. Ensure `data-element` and `aria-*` attributes are correctly emitted in Blade components.
2. Remove any legacy `<style>` blocks from `create-ticket.blade.php`.
3. Align `TicketTypeEnum` labels with institutional naming.

---
*Last Audit: 2026-05-04*
