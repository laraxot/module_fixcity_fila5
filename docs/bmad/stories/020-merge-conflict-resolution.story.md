---
title: Legacy record — Initial merge resolution for TicketComment
status: superseded
module: Fixcity
owner: developer-agent
github_issue: https://github.com/laraxot/fixcity_fila5/issues/20
discussion: https://github.com/laraxot/fixcity_fila5/discussions/20
type: historical-story
created: legacy
updated: '2026-09-26'
tags:
- bmad
- fixcity
qmd: 020 merge conflict resolution.story FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---
## Contesto storico

La relazione `ticketComments()` era stata coinvolta in un conflitto tra l'implementazione legacy `TicketComment` e il modulo `Comment`. La prima risoluzione aveva scelto `Comment::class` anche per la relazione legacy.

## Correzione architetturale successiva

La scelta è stata riconosciuta come incompatibile con lo schema: `ticket_comments` usa `ticket_id`, `user_id`, `content` e soft delete; `comments` usa una relazione polimorfica e campi diversi. La relazione `ticketComments()` deve restare associata al modello legacy `TicketComment`; le nuove discussioni usano la relazione separata `comments()` del modulo Comment.

Non sostituire modello, factory o test con una conversione meccanica. Fonte della correzione: [handoff TicketComment legacy](../../../../../../docs/chat/ticketcomment-legacy-2026-09-26.md).

## Esito

Questa story è `superseded`: conserva il contesto dell'incidente, non la sua prima decisione. La decisione corretta è applicata in `HasTicketRelations::ticketComments()` e coperta dai test legacy del modulo Fixcity.
