---
title: "ingest manifest summary infolist"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-26
qmd: "ingest manifest summary infolist"
issues: []
discussions: []
---

# Ingest manifest: summary-infolist-migration

Files updated and to ingest:

- Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php  # code change: getSummarySchema -> Infolist
- Modules/Fixcity/docs/CreateTicketWizardWidget.md                  # doc update: mention Infolist usage and example
- Modules/Fixcity/docs/stories/wizard-summary-infolist-alignment.md # story doc: rationale and steps

Notes:
- Please run the local wiki ingestion pipeline (qmd or custom importer) to index these documents into the LLM wiki.
- If any duplicate docs exist in Themes/Sixteen/docs referencing the old SchemaView partial, update them to reference the infolist guidance.
