---
title: "Decision log — inbound marketing"
type: decision-log
updated: 2026-10-15
status: draft
tags: [docs, fixcity, bmad]
qmd: "decision log fixcity"
---

# Decision log

## 2026-10-15 — Scopo prima del feature set

- **Decisione:** fiducia + capacità + adozione B2G sono il criterio; non pageview
  o banner.
- **Artefatto:** [purpose.md](./purpose.md).

## 2026-10-15 — Blueprint = wiring dei campi esistenti

- **Decisione:** riusare `Article`/`ArticleResource`, senza migrazione o modulo
  Eventi; l'implementazione futura coprirà form/table/query.
- **Ragione:** Ponytail/YAGNI e dati già presenti.

## 2026-10-15 — Terzo riferimento: Asso della Vendita

- **Decisione:** usare AFC, Alessandro Ferrari e Asso della Vendita come pattern
  qualitativi di promessa verticale, cluster, playbook, CTA progressive e
  misurazione.
- **Adattamento:** FixCity trasferisce metodo e chiarezza in un contesto civico,
  senza tono commerciale, lead scoring, tracking cross-site o gate obbligatori.
- **Artefatti:** `afc-aferrari-asso-pattern-analysis.md` e
  `content-operations-plan.md`.

## 2026-10-15 — AFC come pattern inbound, non come template

- **Decisione:** studiare afcformazione.it per funnel (promessa, metodo, risorse,
  blog, CTA progressive); adattare a FixCity civico; non copiare stack WP.
- **Artefatto:** [afc-formazione-pattern-analysis.md](./afc-formazione-pattern-analysis.md)

## 2026-10-15 — Blog + eventi nel piano inbound

- **Decisione:** sì a blog ed eventi come canali P0 inbound; series email P2;
  ads no.
- **Perché:** fiducia e qualificazione B2G > inventorio pubblicitario.

## 2026-10-15 — Post agnostico, Article come implementazione tecnica

- **Decisione:** nel prodotto il contenuto è un **post**; il modello tecnico
  esistente resta `Article`, con `content_type=post` per il post editoriale
  e `content_type=event` per l'evento.
- **Perché:** il termine post non vincola il dominio al solo blog e consente di
  riusare Blog, Comment, Media e SEO senza creare un modulo Event. Il campo
  tecnico `type` è riservato alla gerarchia STI di Parental.

## 2026-10-15 — Solo documentazione

- **Decisione:** nessun codice Filament/Blade in questa fase.

## 2026-10-15 — Design Comuni prima dell'abilitazione

- **Decisione:** blog ed eventi seguono i pattern ufficiali `argomenti`,
  `argomento`, `lista-risorse`, `lista-risorse-categorie`, `novità`,
  `novità-dettaglio`, `eventi` ed `evento-dettaglio`.
- **Route canoniche:** `/blog` e `/events`; `Novità` è il pattern visuale, non
  un secondo dominio editoriale.
- **Perché:** mantenere tassonomia, liste, dettaglio, breadcrumb, allegati,
  feedback e informazioni evento coerenti con Design Comuni prima del runtime.

## 2026-10-15 — Header/footer JSON come unico punto di navigazione

- **Decisione:** i link Blog/Eventi sono contenuti in `header.json` e
  `footer.json`; Blade resta il vestito.
- **Perché:** evitare copy e link divergenti tra tema e contenuto tenant.
- **Guardia:** nessuna implementazione FO/Filament finché il readiness report è
  `CONCERNS`.

## 2026-10-15 — Scopo approfondito + Ponytail/Blueprint allineati

- **Decisione:** `purpose.md` è SSoT del *perché*; Blueprint/Ponytail sono il
  *come minimo* su `Article` esistente; gap form/table/query documentati, no codice.
- **Perché:** utente chiede capire a fondo lo scopo prima di tool/stack.
- **Stato:** discovery CONCERNS.

## 2026-10-15 — Asso deep + skill/rule/memory + improvement

- **Decisione:** approfondire AssodellaVendita (fiducia post-promessa, frizioni
  adozione, playbook); creare wiki skill/rule/memory `fixcity-inbound-editorial*`;
  aggiungere stories IM-FIXCITY-002/003 e IM-BLOG-006; documentare miglioramenti
  in `improvement-reasoning.md`.
- **Perché:** richiesta esplicita di skills/rules/memories + tutte le stories BMAD
  dopo studio AFC / aferrari / Asso.
- **Artefatti:** `asso-pattern-deep-study.md`, `improvement-reasoning.md`,
  wiki skill/rule/memory, stories Fixcity/Blog citate in `stories-index.md`.
- **Stato:** discovery CONCERNS; nessun runtime.

## 2026-10-15 — Demo content: Article event, non modulo Event

- **Decisione:** popolare demo con `DemoContentSeeder` (poi allineato a
  `post|event`), Comment morph, asset Sixteen locali; **non** creare
  `Modules/Event` finché non servono iscrizioni/capienza/ICS/pagamenti.
- **Perché:** Existing Assets First + demo senza Lorem; Blog+Comment bastano.
- **Artefatti:** brainstorming-event-module-vs-article-type.md · IM-BLOG-007.
- **Verifica:** seed 13 pezzi OK in ambiente locale.

## 2026-10-15 — Post agnostico (`content_type=post|event`)

- **Decisione:** nel dominio dire **post**; discriminator DB =
  `content_type ∈ {post,event}` (non `article`); classe PHP `Article` resta
  (rename = IM-BLOG-008 deferred); mai usare Parental `type` per la tassonomia.
- **Perché:** «post» è più agnostico di «article» (guida, novità, caso, briefing).
- **Artefatto:** Blog `docs/brainstorming-post-vs-article.md` ·
  `docs/bmad/content-type-contract.md`.
- **Correzione:** bozze intermedie con `content_type=article` sono stale.

## 2026-10-15 — Design Comuni otto pagine prima di abilitare Blog/Eventi

- **Decisione:** studiare argomenti/risorse/novità/eventi Design Comuni *prima*
  di abilitare FO; etichette cittadino = «Novità»/«Eventi»; path = `/blog`/`/events`;
  navigazione solo via `header.json`/`footer.json` (local + Fixcity + eu).
- **Perché:** contratto cognitivo comunale > «blog marketing»; JSON-driven nav
  già è il meccanismo corretto (Ponytail).
- **Gate:** JSON aggiornato; route FO ancora assenti → **non abilitato**.
- **Artefatto:** [../design-comuni-static-pages-study.md](../design-comuni-static-pages-study.md)
  · story IM-FIXCITY-004.

## 2026-10-15 — Piano editoriale a fondo AFC+aferrari

- **Decisione:** adottare come SSoT del piano
  [editorial-plan-afc-aferrari.md](./editorial-plan-afc-aferrari.md) basato su
  indici + articoli campione (reclami; KPI PMI; journey B2B).
- **Perché:** AFC = presa in carico operativa; aferrari = journey/KPI/Existing
  Assets First; FixCity serve entrambi i registri.
- **Stato:** discovery; catalogo A0–A5 + E1–E2; no codice.

## 2026-10-15 — Skill/rule/memory da studio AFC + aferrari + Asso

- **Decisione:** formalizzare in wiki skill/rule/memory i pattern dei tre siti
  (operativo / sistemico / sblocco fiducia-adozione) e collegarli alle stories
  IM-FIXCITY-002/003 e IM-BLOG-006.
- **Perché:** gli agenti devono caricare il *perché* prima di produrre contenuti
  o codice; lo studio Asso (fiducia, CRM) chiude il gap «promessa vs consegna».
- **Stato:** docs complete; readiness CONCERNS; no runtime.

## 2026-10-15 — Lettura live otto URL + conferma JSON

- **Decisione:** ribadire gate: nessuno «enable» FO finché mancano route
  `/blog`/`/events` e parity Design Comuni; JSON header/footer già completi
  (module+local+eu). Argomento = hub aggregatore (Cms), non secondo blog.
- **Artefatti:** studio Fixcity aggiornato; Sixteen
  `design-comuni-novita-eventi-fo.md`; wiki concept gate.

## 2026-10-15 — Discussione multi-agente Event vs Article (demo)

- **Decisione (ribadita):** **NO** modulo `Event` per demo/pilota; **SÌ**
  `Article` con `content_type=post|event`; commenti `Modules\Comment`; media
  Design Comuni locali con alt+credito.
- **Perché:** Existing Assets First; valore demo = storytelling civico, non
  RSVP/capienza/pagamenti.
- **Artefatti:** brainstorming-event-module-vs-article-type.md (agenti) ·
  DemoContentSeederTest rafforzato · IM-BLOG-007.

## 2026-10-15 — Risposta utente «post con type=article»

- **Decisione:** confermare post agnostico; **non** usare `type=article`
  (Parental) né canone `content_type=article`. Canone = `content_type=post|event`.
- **Perché:** post è più agnostico di article; Parental `type` è STI.
- **Artefatti:** brainstorming-post-vs-article.md (sezione dedicata) ·
  content-type-contract · memoria wiki `blog-post-agnostic-content-type`.

## 2026-10-15 — Completamento FO `/it/blog` (Novità)

- **Decisione:** lista con «In evidenza» (`featuredPosts` sola lettura);
  dettaglio SEO title da Article; `ResolvePageAction` blog solo
  `content_type=post`; alt/caption/credit + risposte commenti.
- **Perché:** parity Design Comuni `novita` / `novita-dettaglio` senza
  side-effect su `show_on_homepage`.
- **Artefatti:** Themes/Sixteen docs bmad story+blueprint inbound-fo-blog ·
  BlogFrontOfficeNovitaTest.

## 2026-10-15 — Completamento FO `/it/events` (Eventi)

- **Decisione:** mirror di `/blog` con `content_type=event`;
  «Eventi in evidenza» (`featuredEvents` sola lettura); lista
  `paginatedEvents` (tutti i pubblicati, non solo coming-soon);
  `ResolvePageAction` events solo `findPublishedEventBySlug`;
  dettaglio con date, credit, risposte. Nessun modulo `Event`.
- **Perché:** Design Comuni `eventi` / nav JSON `/events`; Ponytail su Article.
- **Artefatti:** inbound-fo-events story+blueprint · blocks/events/{index,detail}.
