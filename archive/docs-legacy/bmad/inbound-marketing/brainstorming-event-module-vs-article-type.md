---
title: "Brainstorming — modulo Event vs Article content_type=event"
type: brainstorming-report
status: decision
updated: 2026-10-15
tags: [bmad, ponytail, blog, event, comment, demo]
qmd: "Event module vs Article content_type event demo seeder Comment"
issues: []
discussions: []
---

# Domanda

Per FixCity, gli eventi pubblici sono un **nuovo modulo `Event`** oppure un
**post Blog** con `content_type`/`type` = `event`?

# Discussione (agenti + evidenza codice)

| Voce | Argomento | Contro |
|---|---|---|
| **Architect / Ponytail** | `Article` ha già date evento, media, SEO, Filament Resource, `HasComments` | Campi “operativi” (capienza, ICS, pagamento) mancano |
| **Inbound / Design Comuni** | Novità ed Eventi condividono lista/dettaglio/argomenti; un dominio editoriale riduce duplicazione FO | Vocabolario cittadino dice “Eventi”, non “Articoli” |
| **Demo / Marketing** | Un seeder popola post+eventi+commenti+foto con lo stesso pipeline | Seeder Event separato = doppio catalogo |
| **Future Event module** | Solo se nascono iscrizioni, posti, ricorrenze, ICS, fee, notify operative | Oggi YAGNI / over-engineering |

Evidenza repo (2026-10-15):

- `Article` fillable: `type`, `content_type`, `event_start_date`, `event_end_date`, `main_image_url`, blocks
- `HasComments` sul modello Article → modulo Comment (morph + approved_at + parent_id)
- `DemoContentSeeder` già cataloga post|event con asset Design Comuni
- Nessun `Modules/Event` presente; crearlo ora duplicherebbe authoring

# Verdict (GO)

**GO: evento = post Blog con `content_type` = `event`.**
**GO: novità/guida = stesso modello con `content_type` = `post`.**

Il nome di dominio agnostico è **post** (non “article”). La classe PHP resta
`Article` finché non parte IM-BLOG-008 (rename).

⚠️ **Mai** scrivere `post`/`event`/`article` nella colonna Parental `type`
(`HasChildren`). Solo `content_type`
(migration `2026_10_15_000001_add_content_type_to_articles_table`).

Linguaggio prodotto:

- Concetto: **post** · FO Design Comuni: **Novità** / **Eventi**
- Path: `/blog` / `/events`
- Codice: `Article` + `content_type ∈ {post,event}`

Vedi [brainstorming-post-vs-article.md](../../../../Blog/docs/brainstorming-post-vs-article.md).

**Upgrade path (quando aprire `Modules/Event`):**

1. Iscrizioni con capienza e lista d’attesa
2. Sessioni multiple / ricorrenze / export ICS
3. Pagamenti o ticket a pagamento
4. Notifiche operative dedicate oltre il nurture deferred

Fino ad allora: Filament Blueprint = wiring form/table sul Resource esistente.

# Implicazioni demo

- Seeder: `Modules\Blog\Database\Seeders\DemoContentSeeder`
- Factory: `ArticleFactory::demoArticle()` / `demoEvent()`
- Commenti: `Comment` morph su Article (approvati + pending + reply)
- Media: `main_image_url` + blocco image con alt/caption/credit (asset locali Sixteen)
- Guardia ambiente: `local|testing|demo`
- Orchestrazione: chiamato da `FixcityDemoSeeder` (step Blog/Eventi)

Vedi anche [brainstorming-demo-blog-eventi.md](./brainstorming-demo-blog-eventi.md).

## Discussione multi-agente (2026-10-15)

| Agente | Verdict |
|---|---|
| Architect / Ponytail | **NO-GO** `Modules/Event` per demo; **GO** `Article.content_type=event` |
| Content strategist civico | 8 Novità + 4–5 Eventi, commenti misti + 1 pending + thread risposta PA; media con alt+credito |
| QA | Seeder già ricco; test doveva usare `Modules\Comment\Models\Comment` (non Blog legacy) |

### Criteri che aprirebbero `Modules/Event` (≥2 insieme)

1. Iscrizioni/RSVP con stati e lista d’attesa  
2. Capienza / concorrente  
3. ICS / sync calendario istituzionale  
4. Pagamenti / biglietti  
5. Check-in / certificati  
6. Integrazioni esterne (Eventbrite, PagoPA eventi)  
7. Ruoli organizzatore distinti da editor Blog  
8. Volumetria agenda multi-sede che satura `Article`

Fino ad allora: **un dominio inbound, due facce FO** (Novità/Eventi).

## Discussione multi-agente (2026-10-16)

Fonti Design Comuni rilette:
[novita](https://italia.github.io/design-comuni-pagine-statiche/sito/novita.html),
[novita-dettaglio](https://italia.github.io/design-comuni-pagine-statiche/sito/novita-dettaglio.html),
[eventi](https://italia.github.io/design-comuni-pagine-statiche/sito/eventi.html),
[evento-dettaglio](https://italia.github.io/design-comuni-pagine-statiche/sito/evento-dettaglio.html),
[argomenti](https://italia.github.io/design-comuni-pagine-statiche/sito/argomenti.html),
[argomento](https://italia.github.io/design-comuni-pagine-statiche/sito/argomento.html),
[lista-risorse](https://italia.github.io/design-comuni-pagine-statiche/sito/lista-risorse.html),
[lista-risorse-categorie](https://italia.github.io/design-comuni-pagine-statiche/sito/lista-risorse-categorie.html).

| Agente | Verdict |
|---|---|
| Architect / Ponytail | **KEEP** `Article.content_type`; **NO** `Modules/Event` — YAGNI, modello già completo per FO statico |
| Explore (nav JSON) | Header/footer SSoT+local+eu: Novità=`/blog`, Eventi=`/events` **già complete** |
| Explore (demo) | `DemoContentSeeder` 8 post + 5 event + Comment morph; readiness ~86→90+ dopo featured eventi |

**Scopo (perché):** il cittadino vede «Novità» e «Eventi» come in Design Comuni;
il dominio resta un solo authoring Blog. L’argomento (`argomento.html`) aggrega
novità+eventi+servizi — hub Cms, non motivo per creare Event.

Commenti FO autenticati: modulo Comment + Filament `CommentsWidget`
([fo-comments](../../../../Comment/docs/bmad/fo-comments)).
