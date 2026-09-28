---
tags: [documentation]
qmd: "second brain"
issues: []
discussions: []
title: "Second Brain — indice architetturale Fixcity"
type: index
module: Fixcity
created: 2026-09-26
updated: 2026-09-27
---

# Second Brain Fixcity

La fonte canonica per le decisioni architetturali del modulo è
[`FixCity — contratto architetturale canonico`](wiki/concepts/fixcity-architecture-contract-2026-09-26.md).
Aggiorna quella fonte quando cambia una regola; non duplicare qui il contenuto.

Per il contesto di processo e il recupero on-demand consulta
[`second-brain-local-discipline`](wiki/concepts/second-brain-local-discipline.md).
Le decisioni di prodotto, i flussi attore e i criteri di accettazione vivono invece nei documenti
[`docs/bmad/`](bmad/README.md).

I workflow eseguibili sono indicizzati in [`docs/bmad/workflows/`](bmad/workflows/README.md)
(process 00–10 + attori) e nel [`workflow-catalog`](bmad/workflow-catalog.md).
Non creare una seconda sequenza equivalente in questo indice.

### Demo FO (seed + guest)

- Seed: [`demo-tickets-presentation-seed`](wiki/concepts/demo-tickets-presentation-seed.md) via `FixcityDatabaseSeeder`
- Story demo guest: [`STORY-513`](bmad/stories/STORY-513-public-guest-visual-demo.md)
- Log: [`wiki/log.md`](wiki/log.md)


## Documentazione Architetturale

### 5. PHPStan generics
Le relazioni Eloquent devono dichiarare entrambi i tipi del generic (`RelatedModel` e
`DeclaringModel`). I docblock vanno verificati dopo aver pulito la result cache; non si
accettano falsi positivi documentati né `@phpstan-ignore` come sostituto del fix.

**Fonte**: `app/Models/Concerns/HasTicketRelations.php`

### 6. Costanti tipizzate
Le costanti array di dominio devono dichiarare `array` quando il gate `typeCoverage`
richiede la copertura della dichiarazione. Il gate si corregge nel codice, mai nel neon.

### 7. UserContract pattern (non User model direct usage)
Sempre usare `UserContract` per l'autorizzazione, mai `Modules\User\Models\User` direttamente. Questo garantisce l'astrazione del modello utente e permette implementazioni diverse.
