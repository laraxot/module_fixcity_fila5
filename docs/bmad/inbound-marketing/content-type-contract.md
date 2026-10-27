---
title: "Contratto contenuti Blog/Eventi — post agnostico"
type: decision-record
status: superseded
updated: 2026-10-27
superseded_by: ../../../../Blog/docs/stories/article-type-parental-sti-post-event.story.md
tags: [bmad, post, content-type, parental, blog]
qmd: "content_type post event Article Parental type contratto"
---

# Contratto `content_type` — post agnostico

> **Superato (2026-10-27).** Il discriminatore reale e' `Article.type` (`post` | `event`, Parental STI con `Post` e `Event`), non
> `content_type`. Il glossario a tre piani e il termine «post» agnostico restano utili come linguaggio; la regola sui campi no. Vedi la
> [story Parental](../../../../Blog/docs/stories/article-type-parental-sti-post-event.story.md).

Il dominio riusa `Modules\Blog\Models\Article`. Non si introduce un modulo
`Event` né si usa Parental `type` come rubrica editoriale.

## Glossario a tre piani

| Piano | Termine | Esempio |
|---|---|---|
| FO Design Comuni | Novità / Eventi | menu header/footer |
| Dominio (agnostico) | **post** / event | brief, story, seeder |
| Codice PHP (oggi) | classe `Article` | Eloquent, Filament, morph |
| DB editoriale | `content_type` | `post` \| `event` |
| DB STI Parental | `type` | `NULL` o FQCN figlia |

**Non** dire «post con type=article»: il campo giusto è `content_type=post`.

| Campo | Valori | Uso |
|---|---|---|
| `content_type` | `post` \| `event` | tassonomia editoriale |
| `Article.type` | `NULL` / FQCN | solo Parental `HasChildren` |
| `event_start_date`, `event_end_date` | datetime | obbligatori se `content_type=event` |

## Regole

1. `content_type=post` → pezzo editoriale (Novità / guida / caso).
2. `content_type=event` → appuntamento datato (Eventi).
3. Mai `type=article` o `type=event` o `type=post` (STI).
4. Mai adottare `content_type=article` come canone (legacy solo in lettura).
5. Query Eventi: prima `content_type=event`, poi pubblicazione, poi date.
6. Le date sole non fanno di un post un evento.

## Superfici

Filament: Select `content_type` (label «Post / Novità», «Evento»).  
Sixteen: etichette Novità/Eventi; path `/blog` `/events`.  
Demo: `DemoContentSeeder` scrive solo `post|event`.  
Rename classe: [IM-BLOG-008](../../../../Blog/docs/stories/inbound-rename-article-to-post.story.md) deferred.

SSoT brainstorm: [brainstorming-post-vs-article.md](../../../../Blog/docs/brainstorming-post-vs-article.md).
