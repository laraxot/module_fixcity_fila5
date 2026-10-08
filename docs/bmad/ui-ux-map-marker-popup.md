---
title: "BMAD — Map marker popup expected state"
type: bmad-ui-contract
module: Fixcity
owner: Modules/Fixcity + Modules/Geo
status: approved
created: 2026-09-27
---

# Expected experience

## Design-Comuni alignment

The public list follows the reference pattern of filters, map and expandable report information in the [Design-Comuni reports list](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html). The authenticated [personal area](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-area-personale.html) and the submission [summary](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html) / [confirmation](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-04-conferma.html) remain separate flows. A public marker therefore opens the readable public ticket detail.

When a guest selects a public report marker, the popup must be immediately understandable and actionable at desktop and mobile widths.

The popup must show, in this order:

1. report title, with a maximum of two readable lines;
2. current status as a high-contrast badge;
3. report type/category;
4. address, or an explicit translated unavailable value;
5. a short public description when available;
6. a primary, highly visible **Details** link that opens the public report detail experience;
7. a secondary close action.

The loading state must reserve the same layout space and clearly communicate that details are loading. A failed detail request must still leave the title, status, type, address and Details action usable.

The popup must never depend on Italian literals in JavaScript. Labels are locale-aware and the detail URL must preserve the active locale. The action must be keyboard reachable, have visible focus, and remain usable at 320px viewport width.

## Definition of done

- Clicking a marker opens a readable popup without clipped content.
- The primary action is labelled “Dettagli” in Italian and “Details” in English.
- The primary action is visually dominant and has a minimum 44px target height.
- The action opens the public detail modal/page, not the back office.
- Popup content is verified at 320, 768 and 1440px with Puppeteer.
