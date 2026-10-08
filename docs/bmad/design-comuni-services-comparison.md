---
title: "Design-Comuni services — FixCity comparison and correction record"
type: bmad-gap-analysis
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
sources:
  - https://italia.github.io/design-comuni-pagine-statiche/index.html
  - https://italia.github.io/design-comuni-pagine-statiche/sito/servizi.html
  - https://italia.github.io/design-comuni-pagine-statiche/sito/servizi-categoria.html
  - https://italia.github.io/design-comuni-pagine-statiche/sito/servizio-dettaglio.html
---

# Expected experience

The official model presents a task-oriented service catalogue: page title and
explanation, keyword search, service count/list, highlighted services, category
exploration, feedback, contact/help, issue reporting and a structured footer.
The service detail continues with audience, description, procedure, required
items, outcome, deadlines, cost, access CTA, contacts and related content.

# Before/after comparison

| Area | Before | Correction |
| --- | --- | --- |
| Discovery | Six highlighted cards only | Searchable highlighted cards plus category directory |
| Categories | Navigation anchors without an actual directory | Six keyboard-focusable category cards with descriptions |
| Localization | Card status, “featured” and “access” fallback text was Italian | All reusable card copy uses `pub_theme::services.card.*` |
| Accessibility | Search had no explicit accessible name or controlled region | Label, `aria-controls`, focus-visible category links and stable headings |
| Architecture | No new backend endpoint required | Folio Blade view + Alpine state; no controller/service introduced |

# Remaining scope

The reference contains a full municipality-wide catalogue and service-detail
content model. FixCity currently has a focused demo catalogue. A future story
should connect categories and detail pages to persisted service content if the
product expands beyond reporting civic issues.

## Follow-up implementation audit — 2026-09-27

The first alignment pass established search and category-card structure, but a
deeper owner-data audit found that those cards implied operational services for
which the application has no verified data or integrations. The active Sixteen
page now limits the entry point to the implemented report, browse and track
journeys, with real locale-aware Folio links. Search filters those visible tasks
and announces count/empty feedback in all four supported locales. Unverified
municipal contacts and inactive service examples were removed. Detailed
expectations and browser evidence live in Sixteen BMAD Story 519; the complete
81-template coverage audit and workflow gaps remain tracked by STORY-517.
