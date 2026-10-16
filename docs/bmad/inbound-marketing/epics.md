---
title: "Epics — inbound blog ed eventi"
type: epics
status: proposed
updated: 2026-10-15
---

# Epic map

## Epic I — Governance editoriale

- **I.1** Owner, revisore, tono di voce civico
- **I.2** Calendario 90 giorni (articoli + eventi)
- **I.3** Privacy iscrizione / email (Gdpr + Notify)

## Epic II — Contenuti pilota

- **II.1** 4–6 articoli evergreen (outline → draft)
- **II.2** Hub risorse «poche e utili» (Cms outline)
- **II.3** 1 evento pilota (programma + a11y + follow-up)

## Epic III — Authoring Filament (post-gate)

- **III.1** Blueprint Article content_type post|event (già in docs Blog)
- **III.2** Filtri tabella / validazione date evento
- **III.3** FO Sixteen liste/dettaglio (Design Comuni)

## Epic IV — Nurture (deferred)

Serie email stile AFC briefing — solo dopo I.3 e policy.

## Epic V — Sistema editoriale e misurazione

- **V.1** Pillar, cluster e tassonomia editoriale;
- **V.2** Playbook/checklist come risorse operative;
- **V.3** CTA progressive e link governance;
- **V.4** KPI di utilità, partecipazione e adozione senza vanity metrics;
- **V.5** Prove di fiducia post-promessa (cittadino);
- **V.6** Frizioni di adozione operatori PA (B2G).

## Epic VI — Dialogo FO (Comment module)

Owner docs: `Modules/Comment/docs/bmad/fo-comments/`.
Stories CMT-016…021 — Filament `CommentsWidget` su blog/eventi/ticket;
Zero Livewire standalone. Pattern fiducia post-promessa (Asso) adattato a PA.

## Story registry

| Epic | Story IDs |
|---|---|
| I | IM-BLOG-001, IM-GDPR-001, IM-MEDIA-001, IM-TENANT-001, IM-LANG-001 |
| II | IM-BLOG-005, IM-BLOG-006, IM-CMS-001, IM-UI-001, IM-MEETUP-001, IM-FIXCITY-005 |
| III | IM-BLOG-002, IM-BLOG-003, IM-SEO-001, IM-SIXTEEN-001, IM-FIXCITY-004 |
| IV | IM-NOTIFY-001 (deferred) |
| V | IM-BLOG-004, IM-FIXCITY-001, IM-FIXCITY-002, IM-FIXCITY-003 |

Nessuna storia Epic III/IV è `ready-for-dev` finché readiness = CONCERNS.

Ragionamento miglioramenti: [improvement-reasoning.md](./improvement-reasoning.md).
Asso deep: [asso-pattern-deep-study.md](./asso-pattern-deep-study.md).
