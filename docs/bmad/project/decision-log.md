# Decision Log — FixCity

### 2026-10-16 — BMAD dossier ticket lifecycle distribuito per ownership
- **Decision:** Il planning e il brainstorming del ciclo ticket vivono nel modulo
  Fixcity; Comment documenta il contratto del widget; Blog documenta il dettaglio
  editoriale; Sixteen documenta la proiezione Design Comuni. Il Filament Blueprint
  resta nel modulo owner del dominio.
- **Rationale:** Second Brain richiede documenti vicini al codice proprietario;
  evita duplicazione tra stato/persistenza e presentazione. `TicketActivity`,
  CommentWidget e Media Library restano le fonti uniche.
- **Made by:** BMAD brainstorming + planning + ECC Blueprint + Filament Blueprint.
- **Supersedes:** nessuno.

A threaded, append-only record of decisions made across BMAD planning workflows.
Every later skill (brief, PRD, architecture, stories) appends here so the reasoning
behind the plan stays visible and consistent.

**How to use:** add a new entry at the top of the log (newest first). Never rewrite
or delete past entries — supersede them with a new entry that references the old one.

## Entry format

```
### YYYY-MM-DD — <short title>
- **Decision:** <what was decided>
- **Rationale:** <why; alternatives considered>
- **Made by:** <skill/workflow, e.g. bmad-init, prd, architecture>
- **Supersedes:** <link to prior entry, if any>
```

---

### 2026-10-15 — Piano editoriale inbound (studio AFC blog + aferrari articoli)
- **Decision:** Piano 90 giorni FixCity da analisi strutturale di
  afcformazione.it/blog e aferrari.it/articoli: pillar×5, titoli A1–A5, eventi
  E1–E2, hub risorse ≤6, template problema→metodo→strumento→FAQ→CTA.
  Docs-only; nessun publish.
- **Rationale:** AFC = comportamento operativo; aferrari = sistema/KPI;
  FixCity serve entrambi i registri per cittadino e Comune.
- **Made by:** user prompt studio a fondo + BMAD + second brain.
- **Artefatto:** [editorial-plan-afc-aferrari.md](./inbound-marketing/editorial-plan-afc-aferrari.md).
- **Supersedes:** raffina inbound-marketing-plan in footer-sponsorship (resta valido).

### 2026-10-15 — Inbound: scopo + Blueprint Ponytail (gap form/query)
- **Decision:** Formalizzare lo *scopo* inbound (fiducia→metodo→evento→adozione
  B2G; non ads). Filament Blueprint: riuso Article; gap verificato = form/table
  non espongono ancora `content_type`/date; query `comingSoon` non filtra
  `content_type=event`.
  Nessun codice finché gate editoriali aperti.
- **Rationale:** Capire il perché evita feature theater; Ponytail evita modulo Eventi.
- **Made by:** user prompt «capire a fondo lo scopo» + BMAD + Ponytail + Blueprint.
- **Artefatti:** [purpose.md](./inbound-marketing/purpose.md),
  [ponytail-audit](../laravel/Modules/Blog/docs/blueprints/inbound-blog-events-ponytail-audit.md).
- **Supersedes:** raffina entry inbound AFC dello stesso giorno.

### 2026-10-15 — Inbound: blog ed eventi (pattern AFC Formazione)
- **Decision:** Per inbound marketing FixCity: **sì** a blog + eventi come canali
  P0; hub risorse «poche e utili»; serie email P2 solo con Gdpr/Notify; **no**
  ads. Riuso `Article.type` blog|event (Ponytail). Pattern da
  afcformazione.it (promessa verticale, metodo, CTA progressive) adattati al
  civico — non copiare WP/Elementor/tracking.
- **Rationale:** Qualifica fiducia e lead B2G meglio dei banner; allineato a
  Design Comuni eventi + modulo Blog esistente.
- **Made by:** user prompt inbound AFC + BMAD + second brain.
- **Artefatti:** [inbound-marketing/](./inbound-marketing/README.md).
- **Supersedes:** none (coordina note già in footer-sponsorship/inbound-*).

### 2026-10-15 — Sostenibilità progetto: modello a strati + docs nei moduli
- **Decision:** Documentare come sostenere FixCity con ordine P0 B2G/co-funding,
  P1 grant+partner, P2 membership/donazioni, P3 sponsor, **No** ad-tech FO.
  Ownership distribuita nei `docs/` di Fixcity, Cms, Tenant, User, Gdpr, Media,
  Xot e tema Sixteen; indice BMAD in
  [project-sustainability/](./project-sustainability/README.md). Nessun codice.
- **Rationale:** Pattern civic-tech/OSS PA (co-funding, stewardship, recognition
  senza endorsement). Second brain = docs di dominio, non monolite root.
- **Made by:** user prompt sostenibilità + BMAD + second brain + research OSOR.
- **Supersedes:** none (estende il filone footer-sponsorship, non lo sostituisce).

### 2026-10-15 — Marketing footer: partner/sostenitori sì, banner no
- **Decision:** Per la domanda «Sostieni il progetto + banner sponsor nel
  footer»: direzione raccomandata = sezione **Partner e sostenitori** (loghi
  statici, disclosure, no tracking) + CTA opzionale verso pagina dedicata.
  **Non** raccomandati banner commerciali in linea nel primo rilascio.
  Implementazione bloccata finché restano aperti policy, beneficiario, diritti
  logo, privacy e test fiducia (readiness CONCERNS).
- **Rationale:** Fiducia civica > inventorio ads; WCAG/reflow; multi-tenant;
  allineamento a best practice civic-tech/open-source (pagina sponsor SSoT,
  footer solo recognition sobria). Dossier:
  [footer-sponsorship/README.md](./footer-sponsorship/README.md).
- **Made by:** user prompt marketing BMAD-only + research PA/civic-tech +
  Ponytail (YAGNI su ad-tech).
- **Supersedes:** none (nuovo filone; non tocca DCP-222 footer JSON SSoT).

### 2026-10-15 — Footer: JSON SSoT, Blade solo vestito (come header)
- **Decision:** Confermare D11 anche per il footer: slogan (`brand.description`),
  nome, colonne/gruppi, contatti, legal e bottom links vivono in
  `sections/footer.json` (local/eu/modulo). `<x-section slug="footer">` → Blade
  `design-comuni`/`full`/`v1` solo markup + `FrontofficeUrl`. Social e logo UE
  vuoti finche' l'ente non fornisce dati (D198-4/5). Voci senza URL non sono link.
- **Rationale:** Stesso contratto dell'header; A5 DCP-198; Filament Section puo'
  editare senza deploy Blade. Ponytail: togliere demo Design Comuni, non inventare
  pagine comune.
- **Made by:** user prompt footer JSON + BMAD DCP-222 + second brain + Ponytail.
- **Supersedes:** footer «Nome del Comune» + gruppi demo + social placeholder.

### 2026-10-15 — Hero H1 in Lora (BI serif) + Vite local fonts
- **Decision:** L'H1 home usa **Lora Regular 400** (Bootstrap Italia `.font-serif`,
  nato per leggibilità display); scale h1 BI `clamp(2.5rem,4vw,3rem)` lh 1.2.
  Titillium resta sans UI a 600. Token `--font-serif` / `--dc-font-serif` = Lora.
  Incorporamento: `resources/fonts` + `fonts.css` relative url (DCP-183 / Laravel
  Working with Fonts — Local Fonts nativo; no Google CDN).
- **Rationale:** Regole BI tipografia; Lora self-hosted ha solo 400/700 — non
  forzare 600 sull'h1. Ponytail: riuso stack font gia' presente, un solo H1 serif.
- **Made by:** user prompt + BI docs + Vite fonts docs + BMAD DCP-221 + Ponytail.
- **Supersedes:** H1 Titillium 600/800; `--font-serif: Titillium Web`.

### 2026-10-15 — Tipografia FO: semibold 600, no Black fallback
- **Decision:** Su home `/it` e chrome (brand, nav) i titoli **sans** usano
  `font-weight: 600` e `line-height >= 1.15`; vietato 750/800/900 su Titillium.
- **Rationale:** WCAG / Designers Italia / leggibilità ipovedenti.
- **Made by:** user prompt WCAG + BMAD + second brain + Ponytail.
- **Supersedes:** pesi 700/800 e lh 0.98 (poi raffinato dall'entry Lora hero).

### 2026-10-15 — Header: JSON SSoT, Blade solo vestito
- **Decision:** Confermare e applicare D11: tutto cio' che si vede nell'header
  (slogan=`brand.tagline`, nome, nav, account, lingue, search, follow_us) vive in
  `config/{local,eu}/fixcity/database/content/sections/header.json` (+ copia modulo).
  `<x-section slug="header">` → `Section::getBlocksBySlug`. I tpl `design-comuni` e
  `v1`/`tailwind` non inventano copy; localizzano solo i path (`FrontofficeUrl`).
  Brand FixCity + tagline prodotto nel JSON; social vuoti finche' l'ente non da' URL.
- **Rationale:** Separazione contenuto tenant / presentazione tema; Filament
  SectionResource/HeaderNavBlock puo' editare senza deploy Blade.
- **Made by:** user prompt + BMAD D11 + Ponytail + Filament Blueprint + Second Brain.
- **Supersedes:** copy "Nome del Comune" / hardcode "Seguici su" nel vestito Tailwind.

### 2026-10-15 — Completamento FO con BMAD + ECC + Ponytail + Filament Blueprint
- **Decision:** Continuare il loop di chiusura sul FO/BO solo con fix actionable senza
  decisioni ente: nav header → path FixCity reali + `FrontofficeUrl`; social `#`
  nascosti; sitemap senza path demo; Blueprint F-02/F-03 gia' chiusi (non riaprire).
  Voci menu/comune/PNRR/social reali restano bloccate su D198-1..6.
- **Rationale:** Ponytail (YAGNI): localizzare/rimuovere link morti batte creare
  pagine Design Comuni vuote. ECC plan→implement sul backlog misurato. Blueprint
  gia' audito (DCP-180/181); registro interessato resta in bozza per D1–D7 DPO.
- **Made by:** bmad-help orient + ECC + ponytail + Filament Blueprint skills + Second Brain.
- **Supersedes:** none.

### 2026-10-14 — Registration and account lifecycle compliance gate
- **Decision:** Keep registration privacy acknowledgement, service-term acceptance,
  optional marketing consent, cookie preference and account/rights requests as
  separate contracts. Add Epic 5 stories 5.3–5.6 for versioned legal evidence,
  marketing revocation, subject rights/account lifecycle and multilingual email
  verification.
- **Rationale:** The current registration validates two booleans but does not link
  the user to a versioned published document; marketing has no complete revocation
  surface; account deletion has no approved PA retention matrix; and auth routes
  are not aligned with all enabled locales. A direct deletion cannot be called
  legally complete while ticket, audit and legal-hold treatment is undecided.
- **Evidence:** `bmad-output/registration-compliance-audit.md`,
  `laravel/Modules/Gdpr/app/Filament/Widgets/Auth/RegisterWidget.php`,
  `laravel/Modules/Gdpr/app/Actions/SaveGdprConsentsAction.php`,
  `laravel/Modules/User/app/Actions/User/DeleteUserAction.php`,
  `laravel/config/local/fixcity/lang/it/terms.md`.
- **Made by:** BMAD audit + Second Brain verification.
- **Supersedes:** none.

### 2026-10-14 — Site audit: view cache, legal destinations and runtime boundary
- **Decision:** Add stories 1.4, 6.10 and 6.11 for the concrete gaps found by the
  read-only site audit: unresolved `geo.mappa-static`, unregistered/inconsistent
  localized legal destinations, and Bootstrap runtime still present outside the
  already-correct cookiebar.
- **Rationale:** These are observable release or compliance risks, not speculative
  cleanup. `view:cache` currently fails; the route table lacks the linked legal
  destinations; and production-facing Sixteen assets still import/use Bootstrap.
- **Made by:** ponytail-audit + bmad-epics-and-stories + Second Brain verification.
- **Supersedes:** none.

### 2026-10-14 — Legal/disclaimer research and route-audit correction
- **Decision:** Treat the supplied disclaimer/terms material as a planning checklist,
  not reusable legal text. Add stories 6.12–6.15 for the terms/disclaimer contract,
  owner-approved legal facts, versioned acceptance evidence and publication gate.
- **Rationale:** The repository already has CMS/Folio legal page infrastructure and
  feature tests; the open risk is incomplete tenant content, placeholder terms,
  missing legal-notes/media-policy files and inconsistent links—not a second route
  system. The earlier route-absence finding is corrected.
- **Evidence:** `bmad-output/research-report.md`, Fixcity legal JSON/configuration and
  publication-gate tests.
- **Made by:** bmad-research + bmad-epics-and-stories + Second Brain verification.
- **Supersedes:** A-02 route-absence wording in `site-audit.md`.

### 2026-10-14 — View-cache blocker closed
- **Decision:** Close story 1.4 after correcting the Sixteen test block component
  alias and the syntax error in the canonical static map view.
- **Evidence:** `php artisan view:cache` completed successfully; both changed Blade
  files passed PHP syntax validation and the diff passed whitespace validation.
- **Made by:** Codex implementation following BMAD story 1.4.
- **Supersedes:** A-01 in `site-audit.md` as an open blocker.

### 2026-10-14 — Cookiebar runtime e decisione DCP-136
- **Decision:** La Cookiebar multilingua è sempre integrata nel flusso pubblico quando `GdprData::$cookie_banner_on` è attivo. `statikbe/laravel-cookie-consent` è l'unico runtime: il middleware inietta l'override della view prima di `</body>`, mentre il tema Sixteen fornisce la UI Tailwind/Alpine/DaisyUI con contratto semantico Bootstrap Italia. La preferenza anonima usa `__cookie_consent`; `spatie/laravel-cookie-consent` non viene aggiunto.
- **Rationale:** Il banner serve a informare e raccogliere una scelta granulare prima di analytics/marketing; il modulo Gdpr resta proprietario di trattamenti, revoche e audit autenticato. Un secondo package introdurrebbe cookie e comportamenti concorrenti. La vecchia conclusione "nessun banner" di DCP-136 valeva solo per il precedente inventario di soli cookie tecnici ed è superata dalla decisione di prodotto di integrare sempre la Cookiebar e dalla presenza del flusso a categorie.
- **Made by:** BMAD architecture update + story refinement + Second Brain verification.
- **Supersedes:** la voce Cookiebar del 2026-10-14 che indicava `gdpr_cookie_consent` e `sixteen.layout.cookiebar`, e DCP-136 (2026-10-13) nella sola parte relativa a "nessun banner".

### 2026-10-14 — Cookie Policy localizzata e baseline legale tecnica
- **Decision:** La Cookiebar punta a `/{locale}/cookie-policy`, una pagina Folio localizzata con inventario dei cookie tecnici e di preferenza, link alla privacy e modalità di revoca. I testi del banner e della policy sono gestiti dal namespace `cookie-consent` per it/en/de.
- **Rationale:** Un banner senza una policy raggiungibile e comprensibile non soddisfa il requisito di informazione. La pagina rende verificabile il comportamento tecnico, ma la conformità finale richiede ancora validazione del titolare sull'inventario e sulle finalità effettive.
- **Made by:** BMAD story refinement + Second Brain scope update.
- **Supersedes:** il riferimento precedente a una policy `/cookie-policy` non implementata.

### 2026-10-14 — Cookie compliance story stream
- **Decision:** Shardare la capability in sette story BMAD indipendenti ma sequenziate: runtime Cookiebar (6.3), policy/inventario localizzato (6.4), gating del processing opzionale (6.5), boundary Gdpr/revoca/audit (6.6), QA accessibilità/localizzazione (6.7), approvazione del titolare (6.8) e gate di regressione release (6.9).
- **Rationale:** Un banner isolato non dimostra conformità: servono informazione, consenso, blocco tecnico, revoca, accessibilità, approvazione legale ed evidenza ripetibile. Ogni story resta entro una sessione agente e dichiara il proprio Owned File/Module Scope.
- **Made by:** bmad-epics-and-stories + Second Brain.
- **Supersedes:** la story monolitica 6.3 come unico contenitore del requisito.

### 2026-10-14 — Cookiebar senza Bootstrap runtime
- **Decision:** Mantenere il contratto semantico Bootstrap Italia (`cookiebar`, posizione subito dopo `body`, Preferenze + Accetto) ma implementare il componente con TailwindCSS + Alpine.js + DaisyUI; Lit/Flowbite restano disponibili allo stack senza essere introdotti inutilmente in questo componente.
- **Rationale:** La parità con Bootstrap Italia riguarda markup e UX, non l'importazione di Bootstrap. `data-bs-*`, Bootstrap CSS e Bootstrap JS contraddicono il vincolo architetturale Sixteen.
- **Made by:** bmad architecture update + second brain + implementazione Codex.
- **Supersedes:** la decisione Cookiebar del 2026-10-14 che lasciava `data-bs-accept` nel markup.

### 2026-10-14 — Cookiebar Bootstrap Italia come boundary Gdpr del tema Sixteen
- **Decision:** Rendere la Cookiebar Bootstrap Italia parte del layout pubblico Sixteen, attivata da `sixteen.layout.cookiebar`; usare `ConsentType::COOKIES` come riferimento del modulo Gdpr e il cookie tecnico `gdpr_cookie_consent` solo per la preferenza anonima.
- **Rationale:** Bootstrap Italia richiede la Cookiebar subito dopo l'apertura di `body`. Il modulo Gdpr resta proprietario di consensi, revoche e audit; il tema non duplica quel modello e non tenta di creare un audit server-side per un visitatore anonimo.
- **Made by:** bmad + second brain + implementazione Codex.
- **Supersedes:** la precedente integrazione che leggeva `sixteen.cookiebar` e usava markup/JS non coerenti.

### 2026-10-07 — Track selected: enterprise
- **Decision:** Initialized this project on the **enterprise** track.
- **Rationale:** The brownfield system spans 19 modules, multiple user roles,
  privacy/compliance decisions, staging infrastructure, browser QA and more than 15
  independent completion slices; a PRD, architecture, security and DevOps plan are
  required before implementation.
- **Made by:** bmad-init
- **Supersedes:** none

### 2026-10-07 — Brownfield source of truth
- **Decision:** Treat current code and dated evidence as authoritative; preserve older
  completion claims only as historical context.
- **Rationale:** Existing module docs contain duplicated and sometimes contradictory
  claims. Planning must distinguish implemented, locally verified, staging-verified,
  owner-approved and blocked states.
- **Made by:** bmad-document-project / second brain audit
- **Supersedes:** none

### 2026-10-07 — Completion gate
- **Decision:** FixCity is not release-complete until G1–G7 in `release-plan.md` are
  evidenced on the candidate environment; PHPStan green is necessary but insufficient.
- **Rationale:** The remaining risk is operational and legal (tenant privacy, queues,
  SMTP, backups, staging and sign-off), not merely static typing.
- **Made by:** BMAD gap analysis
- **Supersedes:** none

### 2026-10-08 — Standard di codice: niente Services, mixed ultima spiaggia, const in tipi adatti
- **Decision:** Tutti i Services diventano Spatie Queueable Actions; `mixed` solo come ultima spiaggia; le `const` che
  modellano insiemi diventano enum. Eseguito per cluster a onde in `epic-code-standards-services-mixed-const.md`.
- **Rationale:** richiesta esplicita dell'utente; `no-services-rule.md` gia' in vigore ma non applicata (73 file Services
  presenti, alcuni tornati dopo la migrazione del 2026-07-13).
- **Made by:** utente (standing order), pianificato da Claude
- **Supersedes:** none

### 2026-10-13 — Livewire schema bridge at the shared wizard boundary
- **Decision:** Keep Filament's schema protocol and redeclare
  `callSchemaComponentMethod()` in `XotBaseWizardWidget`, delegating to the
  inherited `InteractsWithSchemas` implementation.
- **Rationale:** Livewire's public-method whitelist did not reliably expose the
  trait method through the schema base during nested wizard requests. A shared
  bridge fixes every Xot wizard without a page-specific workaround or a fake
  method in Fixcity.
- **Evidence:** `bmad-output/investigation-livewire-schema-method.md`
  and the focused `CreateTicketWizardWidgetViewTest`.
- **Made by:** BMAD investigation and runtime fix.
- **Supersedes:** none

### 2026-10-13 — Ticket confirmation is a CMS block with capability-code recovery
- **Decision:** Keep the Filament Wizard as the only creation flow, render confirmation through
  `tickets.confirmation.json`, and append the generated ticket code to the localized redirect.
- **Rationale:** The saved report needs a deterministic Design Comuni receipt even when a session
  flash is lost; the query code is already the public tracking capability and avoids a second page
  implementation. The map is verification UI in the summary, not an editable field after save.
- **Made by:** bmad-architecture / bmad-brainstorm / BMAD story update.
- **Supersedes:** none
### 2026-10-14 — Legal completion continuation

- Added stories 6.16 and 6.17 for the missing public terms/disclaimer surface and
  runtime legal-link normalization.
- Added the terms page contract with safe incomplete rendering; no legal clause,
  legal basis, retention period, identity or approval was invented.
- Kept Statikbe/Gdpr as the only cookie-consent runtime; Spatie remains reference
  material and is not installed as a second banner.
- **Made by:** BMAD story refinement + Designers Italia research + Second Brain.
- **Supersedes:** none.

### 2026-10-14 — Designers Italia design-system research

- **Decision:** Add Epic 8 with stories 8.1–8.8 for the current Designers Italia
  baseline, service outcomes, information architecture, content/SEO, accessibility,
  user research, privacy by design and continuous improvement.
- **Rationale:** The studied Docs Italia page says its historical content is
  superseded but identifies the design disciplines that must be evidenced. FixCity
  must not reduce compliance to visual parity or Bootstrap classes.
- **Made by:** bmad-research + bmad-epics-and-stories + Second Brain.
- **Supersedes:** none.

### 2026-10-14 — Designers Italia current normative baseline

- **Decision:** Treat the current Designers Italia requirements page and Manuale
  operativo as the normative/planning baseline; the historical introduction is a
  superseded map only. Consolidate requirements 4.1–4.8 in story 8.18 before the
  release-readiness story 7.1.
- **Rationale:** The project needs evidence for service design, governance,
  accessibility, privacy, monitoring, platforms, licences and procurement, not
  only visual parity or Bootstrap-like markup.
- **Made by:** bmad-research + bmad-epics-and-stories + Second Brain.
- **Supersedes:** ambiguous “Design Comuni compliant” claims without a pinned version.

### 2026-10-15 — Designers Italia baseline pinned

- **Decision:** Record the current requirements page and operational manual as the
  release baseline, with access date 2026-10-15 and version marker current.
- **Rationale:** A release claim needs a reproducible source and an explicit
  runtime boundary; historical Docs Italia guidance is superseded.
- **Runtime boundary:** TailwindCSS + Alpine.js + Lit + DaisyUI + Flowbite;
  Bootstrap Italia remains reference-only.
- **Evidence:** bmad-output/design-guideline-baseline.md.
- **Made by:** BMAD 8.1 execution + Filament Blueprint/ECC review + Second Brain.
- **Supersedes:** unversioned Designers Italia references.

### 2026-10-15 — Homepage civic headline typography

- **Decision:** Keep Titillium Web for body and general civic UI, and use the
  self-hosted Lora face only for the homepage “Segnalazioni e servizi per la tua
  comunità” headline.
- **Rationale:** The regular editorial face reduces the visually heavy appearance
  while preserving the Bootstrap Italia hierarchy, local Vite asset processing,
  readable sizing and 1.2 line-height at responsive widths.
- **Scope:** Front office homepage only; Filament/admin typography and JSON content
  contracts are unchanged.
- **Made by:** BMAD UX + Ponytail + Second Brain.

### 2026-10-15 — Footer sustainability discovery: no banner by default

- **Decision:** Treat commercial sponsor banners as a rejected default for the
  civic footer. Evaluate instead institutional adoption, grants and a transparent
  «Partner e sostenitori» section; donations require a verified beneficiary and
  reporting model.
- **Rationale:** Marketing research identified neutrality, trust, disclosure,
  accessibility, privacy and conflict-of-interest risks that outweigh the simple
  visibility benefit of banners.
- **Status:** CONCERNS / discovery only. No implementation is authorized.
- **Evidence:** [`footer-sponsorship`](./footer-sponsorship/README.md), research,
  product brief, PRD, architecture, UX and readiness gate.
- **Made by:** BMAD brainstorm + research + product brief + PRD + architecture +
  UX + Ponytail + Second Brain.
