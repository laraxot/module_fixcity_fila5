---
title: Legacy record — Initial Fixcity PHPStan remediation proposal
status: superseded
module: Fixcity
github_issue: https://github.com/laraxot/fixcity_fila5/issues/21
discussion: https://github.com/laraxot/fixcity_fila5/discussions/21
type: historical-story
created: legacy
updated: '2026-09-26'
tags:
- bmad
- fixcity
qmd: 021 phpstan errors fixcity.story FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---
## Correzione del resoconto storico

La diagnosi iniziale trattava `TicketComment` come sostituibile con `Modules\Comment\Models\Comment` e proponeva di convertire factory e test. Un audit successivo dello schema ha dimostrato che i due modelli rappresentano tabelle e contratti differenti. Inoltre la classe `TicketComment` non ha un tag PHPDoc `@deprecated`: è un modello legacy mantenuto per compatibilità, mentre `Comment` è il modello per le nuove discussioni.

La proposta di migrazione contenuta nel resoconto originale è quindi **superata e non va applicata**. La relazione `ticketComments()` e la factory legacy restano sul modello `TicketComment`; i test del modulo Comment devono testare il contratto polimorfico separatamente.

Riferimento: [handoff TicketComment legacy](../../../../../../docs/chat/ticketcomment-legacy-2026-09-26.md). La story resta per tracciare l'analisi PHPStan storica, non come istruzione di refactoring corrente.
