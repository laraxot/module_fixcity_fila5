---
title: "Roadmap di FixCity: milestone M1–M6"
type: note
tags: [roadmap, milestone, github]
created: 2026-09-26
updated: 2026-11-25
qmd: "roadmap milestone go-live conformità pa front office multi-comune qualità governance"
issues: ["#757"]
discussions: ["#749", "#751", "#752", "#753", "#754", "#756"]
status: active
---

# Roadmap di FixCity

La roadmap vive nelle [milestone GitHub](https://github.com/laraxot/base_fixcity_fila5/milestones): ogni issue aperta
appartiene a una milestone, ogni milestone ha una discussione per le decisioni. Questa pagina è la mappa; lo stato
aggiornato (issue aperte e chiuse) sta su GitHub.

| # | Milestone | Obiettivo | Discussione |
|---|---|---|---|
| M1 | [Go-live: produzione online e indicizzata](https://github.com/laraxot/base_fixcity_fila5/milestone/1) | `dev` in produzione, sito indicizzato da Google e Bing, CI attiva | #749 |
| M2 | [Conformità PA: ACN e AgID](https://github.com/laraxot/base_fixcity_fila5/milestone/2) | SaaS qualificato, dati in UE, reversibilità, SPID/CIE, accessibilità, 2FA | #754 |
| M3 | [Front office: parità Design Comuni](https://github.com/laraxot/base_fixcity_fila5/milestone/3) | tema Sixteen uguale al modello Design Comuni | #751 |
| M4 | [Multi-Comune](https://github.com/laraxot/base_fixcity_fila5/milestone/4) | un Comune nuovo = configurazione + database | #752 |
| M5 | [Qualità e architettura](https://github.com/laraxot/base_fixcity_fila5/milestone/5) | PHPStan a zero, debito Arch in discesa, niente Service | #753 |
| M6 | [Agenti, documentazione e governance](https://github.com/laraxot/base_fixcity_fila5/milestone/6) | regole verificabili per agenti e persone | #756 |

## Regole
- Un'issue nuova entra subito in una milestone; se non c'è quella giusta, si propone nella discussione di M6.
- Le decisioni prese in una discussione finiscono in un ADR del modulo owner e nella story BMAD; la discussione linka
  l'ADR.
- Ordine di priorità: M1 sblocca tutto il resto (finché la produzione è indietro e la CI è ferma, il lavoro non si vede
  e non è protetto).

## Documenti di pianificazione precedenti
`roadmap/` (PRD, strategia, piano di lancio) descrive intenti di settembre 2026: va riletto contro le milestone e
aggiornato o archiviato (#757).
