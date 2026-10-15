---
title: Comment System Flow
id: STORY-011
author: BMAD
status: done
priority: high
type: story
module: Fixcity
tags:
- bmad
- fixcity
created: legacy
updated: 2026-09-26
qmd: story 011 comment system flow FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Attore: Comment System

### Cosa fa il sistema
- **Creazione commenti** → Cittadino e PA Operator possono commentare
- **Commenti ereditati** → Vecchi commenti in `ticket_comments` sono legacy
- **Nuovi commenti** → Usano il modulo `Comment` (Commentable)
- **Notifiche** → Commento notifica agli interessati

### Flusso
1. **Commento creatore** → `POST /tickets/{id}/comment` → `Comment::create()`
2. **Commento ereditato** → Legacy `ticket_comments` → compatibilità
3. **Commento nuovo** → Nuovi commenti tramite `Comment` modulo
4. **Notifica** → Commento notifica agli utenti iscritti

### Componenti
- `Comment` Model (modulo Comment)
- `Commentable` Morph
- `CommentContract` (Contracts)
- `CommentFactory` (Factory)
- `CommentResource` (Filament)

### Regole architetturali
- `CommentContract` in `Models/Contracts/`
- `Comment` Model in `Models/`
- Factory in `database/factories/`
- Migration in `database/migrations/`
- `Commentable` Morph per ticket

### Second Brain
- `docs/chat/comment-system-flow.md`
- `docs/wiki/log.md` aggiornato

### Quality Gate
- PHPStan 0 errori su `Modules/Comment`
- Test `tests/Feature/TicketSpatieCommentsTest.php` passa
- Test `tests/Feature/TicketCommentsTest.php` passa
