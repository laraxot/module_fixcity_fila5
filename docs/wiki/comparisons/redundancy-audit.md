---
qmd: "redundancy audit 2026 09 26"
title: "FixCity — audit ridondanza documentale 2026-09-26"
type: comparison
module: Fixcity
confidence: high
created: 2026-09-26
updated: 2026-09-26
tags: [redundancy, docs, second-brain, bmad, canonical, cleanup]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - ./redundancy-overview.md
  - ../concepts/fixcity-architecture-contract-2026-09-26.md
---

# Audit ridondanza documentale

## Misurazione

| Owner | Markdown | Gruppi byte-identici | File coinvolti |
|---|---:|---:|---:|
| `Modules/Fixcity/docs` | 334 | 3 | 6 |
| `Themes/Sixteen/docs` | 1.444 | 96 | 196 |
| `Themes/Barthelemy/docs` | 1.795 | 5 | 11 |

Il dato è stato ottenuto con hash SHA-256 dei file Markdown presenti al momento dell'ultimo controllo. Il conteggio va rifatto dopo ogni consolidamento: non è una metrica da copiare in nuovi report.

I duplicati semantici sono più numerosi perché esistono coppie `ticket/segnalazione`, file datati/non datati e copie tra Barthelemy e Sixteen. I file con contenuto identico non sono automaticamente sostituibili: prima va controllato il grafo dei riferimenti entranti e il ruolo del percorso (`root-md-files`, `raw`, `wiki`, archivio storico).

## Duplicati Fixcity certi

| Canonicalizzazione proposta | Stato |
|---|---|
| `docs/CHANGELOG.md` vs `docs/root-md-files/CHANGELOG.md` | stessa copia; mantenere solo il percorso owner dopo verifica indice |
| `docs/REDUNDANCY_ANALYSIS.md` vs `docs/docs/REDUNDANCY_ANALYSIS.md` | stessa copia; `docs/docs` è sprawl |
| `docs/raw/README.md` vs `docs/wiki/README.md` | stessa copia; scegliere wiki come owner e collegare raw se necessario |

## Famiglie Sixteen da consolidare

- report parity con e senza data;
- `ticket-*` e `segnalazione-*` byte-identici;
- `AGID_CHECKLIST_100.md` e `agid_checklist_100.md`;
- `components-update` con varianti uppercase/underscore;
- roadmap duplicate tra root, `roadmap/` e wiki.

## Politica di consolidamento

1. scegliere un owner canonico per famiglia;
2. aggiungere frontmatter `status: historical` ai report non canonici, se sono ancora utili;
3. sostituire i duplicati con link al canonico in un change set separato;
4. non cancellare file tracciati senza inventario di riferimenti e decisione BMAD;
5. aggiornare gli index del modulo/tema dopo ogni accorpamento.

## Verifica dei riferimenti prima della rimozione

Per ogni candidato usare una ricerca repo-wide del basename e del percorso relativo, poi classificare il risultato:

| Classe | Azione |
|---|---|
| `canonical` | mantenere e indicizzare; gli altri documenti puntano qui |
| `historical` | mantenere solo se conserva decisioni o evidenza temporale |
| `redirect` | sostituire il contenuto con un link al canonico |
| `duplicate` | rimuovere solo dopo aver corretto i riferimenti entranti |

Nel controllo corrente non risultano ancora completate le verifiche entranti per tutte le famiglie Sixteen/Barthelemy; per questo lo stato della story resta aperto. L'audit non deve essere duplicato in un secondo report: questo file è l'inventario corrente per Fixcity.

## Stato

Questo audit è un inventario, non autorizza da solo la cancellazione. Le prossime storie devono procedere per famiglia, iniziando dai duplicati esatti Fixcity e dai doppioni `ticket/segnalazione` di Sixteen.
