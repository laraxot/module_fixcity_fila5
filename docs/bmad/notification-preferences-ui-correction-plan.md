---
title: "FixCity citizen notification preferences — UI correction plan"
type: bmad-implementation-plan
status: in-progress
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, citizen, preferences, i18n, playwright]
qmd: "FixCity preferences German Spanish translation browser regression responsive"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ./notification-preferences-ui-expected-2026-09-27.md
  - ./notification-preferences-ui-comparison-2026-09-27.md
  - ./stories/STORY-013-ticket-notification-preferences.md
---

# Correction plan

1. Add the page description and seven preference strings to FixCity's German
   and Spanish locale catalogues, matching the existing IT/EN keys.
2. Render the feature-specific notification-settings title and description;
   stop using dropdown metadata as page copy.
3. Add a Playwright spec using the existing credential helper; the test logs
   in as the demo citizen but never submits or changes the preference.
4. Check the personal settings page in IT/EN/DE/ES at 320, 390, 768 and 1440
   px for localized text, checkbox-label association, status semantics,
   visibility and horizontal overflow.
5. Capture mobile and desktop screenshots under `/tmp`; keep credentials out of
   the spec and test output.
6. Update STORY-013 and this comparison with actual browser evidence. Keep
   email-provider/staging and manual screen-reader checks open.

## Completion record

Pending translations, implementation and browser verification.
