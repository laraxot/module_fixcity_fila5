---
title: "00 INDEX"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-11-26
qmd: "00 INDEX"
issues: []
discussions: []
status: active
---

# Fixcity Module Documentation Index

> Indice canonico della documentazione del modulo. `docs/` e' stato consolidato sotto
> il limite di 100 file Markdown: restano solo le sorgenti correnti. Tutta la
> documentazione storica (report, piani BMAD completati, wiki locale, audit, roadmap)
> vive in [`archive/docs-legacy/`](../archive/docs-legacy/README.md) ed e' consultabile
> ma non e' evidenza corrente se non richiamata da una story attiva.

## Canonico

- [ADR](./adr/) — decisioni di architettura (8 file, sempre valide);
- [Contratti comportamentali](./contracts/INDEX.md) — input/output/invarianti delle Action;
- [Story attive](./stories/) — solo stato attivo (in-progress, review, in-review, ecc.);
- [Regole](./rules/) — no-services, wizard Filament;
- [Manuale utente](./manuale/README.md) — guide per cittadini, operatori, supervisori, amministratori.

## Guide correnti

- [Ticket wizard frontoffice](./ticket-wizard-frontoffice.md)
- [Convenzioni link frontoffice](./frontoffice-link-conventions.md)
- [Concetti wiki trattenuti](./wiki/concepts/) — `folio-api-no-controllers`, `profiles-uuid-contract`
- [STORY-521 owner assignment demo](./bmad/stories/STORY-521-demo-ticket-owner-assignment.md) — trattenuta per riferimento dal codice

## Archivio

- [Documentazione legacy](../archive/docs-legacy/README.md) — ~820 file storici: report,
  audit, piani BMAD, wiki locale, story completate o superate.

## Regole editoriali

Nuovi Markdown: nome lowercase, senza date/timestamp e mai con suffisso `-1`, `-2` o
`-3`; i nomi storici si rinominano solo dopo una mappa dei riferimenti.
