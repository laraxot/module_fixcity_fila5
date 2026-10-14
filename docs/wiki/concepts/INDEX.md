---
qmd: "INDEX"
issues: []
discussions: []
title: "concepts index — Fixcity"
type: index
tags: [concepts, Fixcity]
created: 2026-05-11
updated: 2026-06-13
status: draft
---

# concepts Index — Fixcity

Concetti specifici del modulo Fixcity. Carica on-demand via `qmd search` o consulta il [trigger map root](../../../../../../docs/wiki/rules/00-TRIGGER_MAP.md).

- [profiles-uuid-contract](./profiles-uuid-contract.md) — owner `profiles`: 1 migrazione, `uuid` + `credits` nullable, bump timestamp
- [xotbasewidget-child-no-explicit-widget-view](./xotbasewidget-child-no-explicit-widget-view.md) — widget che estende `XotBaseWidget`: niente `$view` sul figlio; docblock pigro ok
- [fixcity-ticket-vs-ticket-lang](./fixcity-ticket-vs-ticket-lang.md) — `fixcity::ticket.*` per schema Filament Ticket vs `segnalazione` pubblico
- [tickets-view-cms-folio-page.md](./tickets-view-cms-folio-page.md) — `/it/tickets/{id}` + CMS `tickets.view` (STORY-134)
- [no-controllers-folio-volt-filament](./no-controllers-folio-volt-filament.md) — VIETATO usare Controllers; stack ufficiale Folio + Volt + Filament + Actions
- [folio-api-no-controllers](./folio-api-no-controllers.md) — API `/api/*` via Folio + Actions (STORY-069)
- [ticket-citizen-rating-via-rating-module](./ticket-citizen-rating-via-rating-module.md) — RatingMorph, no colonne su tickets (STORY-071)
- [module-basemodel-rule](./module-basemodel-rule.md) — Ticket extends Fixcity BaseModel (STORY-047)
- [phpstan-pest-testcase-helpers](./phpstan-pest-testcase-helpers.md) — helper TestCase + PHPStan Pest (2026-06-13)
- [testing](./testing.md) — quality gate Pest + PHPStan
- [claude-audit-static](./claude-audit-static.md) — gate 80/0 static + debito lang/Ticket
- [completion-roadmap](../overviews/completion-roadmap.md) — cosa resta per chiudere Fixcity
- [user-journey-map](./user-journey-map.md) — percorsi guest→cittadino→PA→admin→sistema: dovrebbe/vede/può/fatto/manca
- [actor-flow-map](./actor-flow-map.md) — actor → flow → story → Action → stato (tracing tecnico)
- [Viaggio del cittadino: accesso e ritorno alla pagina richiesta](citizen-journey-login-register-return.md) — url.intended, redirectIntended, dashboard 404 (DCP-124)
- [Intestazioni di sicurezza e CSP (DCP-139)](security-headers-and-csp-dcp-139.md) — middleware, config, host misurati, Report-Only, config in cache, lista di controllo
- [admin-map-asset-registry-mismatch](./admin-map-asset-registry-mismatch.md) — admin map asset registry mismatch
- [admin-ticket-create-map-visual-contract](./admin-ticket-create-map-visual-contract.md) - admin ticket create map visual contract
- [ai-harness-fixcity-discipline](./ai-harness-fixcity-discipline.md) - AI harness - disciplina agenti modulo Fixcity
- [ai-harness-module-discipline](./ai-harness-module-discipline.md) - AI harness - disciplina agenti (tutti i moduli)
- [blade-template-location-rule](./blade-template-location-rule.md) - blade template location rule
- [cms-block-driven-page-routing-rule](./cms-block-driven-page-routing-rule.md) - cms block driven page routing rule
- [codebase-gap-matrix](./codebase-gap-matrix.md) - Matrice gap codebase Fixcity - story vs file esistenti
- [composer-phpstan-version-conflict-fix](./composer-phpstan-version-conflict-fix.md) - composer phpstan version conflict fix
- [context-compression-and-summary-infolist-rule](./context-compression-and-summary-infolist-rule.md) - Context compression and wizard summary Infolist rule
- [data-sacred-migrations](./data-sacred-migrations.md) - Dati sacri - migrazioni Fixcity
- [demo-operator-panel-access](./demo-operator-panel-access.md) - FixCity demo operator panel access
- [demo-tickets-presentation-seed](./demo-tickets-presentation-seed.md) - demo tickets presentation seed
- [design-comuni-ods-information-architecture](./design-comuni-ods-information-architecture.md) - DesignComuni ODS information architecture - Fixcity
- [design-comuni-service-workflow-scope](./design-comuni-service-workflow-scope.md) - Design Comuni workflow scope for FixCity
- [design-comuni-theme-css-only-rule](./design-comuni-theme-css-only-rule.md) - design comuni theme css only rule
- [design-comuni-wizard-css-generalization-rule](./design-comuni-wizard-css-generalization-rule.md) - Design Comuni Wizard CSS Generalization Rule
- [farmshops-clustering-integration](./farmshops-clustering-integration.md) - Farmshops.eu Clustering Pattern Integration
- [ff-wizard-visual-parity](./ff-wizard-visual-parity.md) - ff wizard visual parity
- [filament-first-ui-boundary](./filament-first-ui-boundary.md) - Fixcity - boundary UI Filament-first
- [filament-multiple-forms](./filament-multiple-forms.md) - filament multiple forms
- [filament-v5-schema-pattern](./filament-v5-schema-pattern.md) - Filament v5 Schema Pattern - XotBase Abstraction
- [filament5-schema-form-access-rule](./filament5-schema-form-access-rule.md) - filament5 schema form access rule
- [filament5-schema-namespaces-and-wizard-summary](./filament5-schema-namespaces-and-wizard-summary.md) - filament5 schema namespaces and wizard summary
- [filament5-schema-section-namespace-rule](./filament5-schema-section-namespace-rule.md) - Filament 5 Schema Section Namespace Rule For Fixcity Wizard
- [filament5-widget-schema-submit-state-rule](./filament5-widget-schema-submit-state-rule.md) - filament5 widget schema submit state rule
- [fix-complete-only-after-target-route-recheck](./fix-complete-only-after-target-route-recheck.md) - fix complete only after target route recheck
- [fixcity-architecture-contract](./fixcity-architecture-contract.md) - FixCity - contratto architetturale canonico
- [fixcity-best-practices](./fixcity-best-practices.md) - Fixcity Module Best Practices
- [fixcity-gap-compliance-pa-italia](./fixcity-gap-compliance-pa-italia.md) - FixCity - gap normativi/interoperabilità PA italiana (deep dive)
- [freellmapi-boundary](./freellmapi-boundary.md) - FreeLLMAPI boundary - Fixcity
- [frontoffice-no-standalone-livewire](./frontoffice-no-standalone-livewire.md) - frontoffice - no livewire standalone
- [frontoffice-ticket-priority-default-rule](./frontoffice-ticket-priority-default-rule.md) - Frontoffice Ticket Priority Default Rule
- [frontoffice-wizard-theme-runtime-boundary](./frontoffice-wizard-theme-runtime-boundary.md) - Frontoffice Wizard Theme Runtime Boundary
- [global-css-rule](./global-css-rule.md) - Global CSS Rule - Avoid Page-Specific Selectors
- [header-green-branding-rule](./header-green-branding-rule.md) - header green branding rule
- [livewire-cache-table-rate-limiter](./livewire-cache-table-rate-limiter.md) - livewire cache table rate limiter
- [livewire-single-root-element](./livewire-single-root-element.md) - livewire single root element
- [location-capture-map-wizard](./location-capture-map-wizard.md) - location capture map wizard
- [main-module-profiles-ownership](./main-module-profiles-ownership.md) - main module e ownership tabella profiles
- [map-controls-visibility-issue](./map-controls-visibility-issue.md) - map controls visibility issue
- [map-fullscreen-issues](./map-fullscreen-issues.md) - map fullscreen issues
- [migration-update-timestamps-only](./migration-update-timestamps-only.md) - Migrazioni Fixcity - solo updateTimestamps
- [module-artifact-parity-audit](./module-artifact-parity-audit.md) - Fixcity - audit parità artefatti modulo
- [module-congruence-rule](./module-congruence-rule.md) - Regola di congruenza modulo-modello (Fixcity)
- [no-app-support-queueable-actions](./no-app-support-queueable-actions.md) - no app/Support - Fixcity QueueableAction
- [no-page-specific-css-lesson](./no-page-specific-css-lesson.md) - no page specific css lesson
- [node-modules-governance](./node-modules-governance.md) - Node Modules Governance - Project Root Only
- [norme-e-conformita-fixcity](./norme-e-conformita-fixcity.md) - FixCity: norme, conformità e lacune (matrice normativa DCP-54)
- [obsidian-skills-and-ingest-checklist](./obsidian-skills-and-ingest-checklist.md) - Checklist Obsidian, skills e ingest QMD
- [one-migration-per-model-rule](./one-migration-per-model-rule.md) - one migration per model rule
- [phpstan-compliance](./phpstan-compliance.md) - Fixcity Module - PHPStan Type Compliance
- [phpstan-runtime-priority-rule](./phpstan-runtime-priority-rule.md) - phpstan runtime priority rule
- [pub-theme-wizard-zen](./pub-theme-wizard-zen.md) - pub theme wizard zen
- [public-ticket-detail-and-follow](./public-ticket-detail-and-follow.md) - Dettaglio pubblico della segnalazione e funzione Segui
- [second-brain-local-discipline](./second-brain-local-discipline.md) - Second Brain Local Discipline (stub Fixcity)
- [second-brain-operating-model](./second-brain-operating-model.md) - Second Brain Operating Model (Modules)
- [segnalazioni-elenco-blocks](./segnalazioni-elenco-blocks.md) - segnalazioni elenco blocks
- [segnalazioni-elenco-map-architecture](./segnalazioni-elenco-map-architecture.md) - segnalazioni elenco map architecture
- [static-analysis-workflow](./static-analysis-workflow.md) - static analysis workflow
- [test-page-routing](./test-page-routing.md) - test page routing
- [theme-owned-wizard-css-parity-rule](./theme-owned-wizard-css-parity-rule.md) - theme owned wizard css parity rule
- [ticket-activity-event-contract](./ticket-activity-event-contract.md) - Contratto eventi TicketActivity - payload e visibility
- [ticket-bootstrap-tailwind-conversion](./ticket-bootstrap-tailwind-conversion.md) - Segnalazione Bootstrap → Tailwind Conversion
- [ticket-citizen-timeline](./ticket-citizen-timeline.md) - Timeline cittadino - storico avanzamento segnalazione
- [ticket-crea-error-analysis](./ticket-crea-error-analysis.md) - ticket crea error analysis
- [ticket-crea-geolocate-when-empty](./ticket-crea-geolocate-when-empty.md) - ticket crea geolocate when empty
- [ticket-crea-map-fullscreen-contract](./ticket-crea-map-fullscreen-contract.md) - ticket crea map fullscreen contract
- [ticket-crea-map-ux-fixes](./ticket-crea-map-ux-fixes.md) - Segnalazione Crea Map UX Fixes
- [ticket-crea-step1-diff](./ticket-crea-step1-diff.md) - Segnalazione-Crea Step 1 - Diff Visivo con Design Comuni (2026-05-04)
- [ticket-crea-url-routing-chain](./ticket-crea-url-routing-chain.md) - ticket create URL - routing chain CMS + PageSlugMiddleware
- [ticket-data-step-spacing-rule](./ticket-data-step-spacing-rule.md) - Segnalazione Data Step Spacing Rule
- [ticket-design-comuni-comparison](./ticket-design-comuni-comparison.md) - ticket design comuni comparison
- [ticket-detail-page-ux](./ticket-detail-page-ux.md) - Ticket Detail Page UX Design - Static Map + Comments
- [ticket-fo-page-design](./ticket-fo-page-design.md) - Ticket FO page design - mappa statica + comments
- [ticket-header-navbar-green-rule](./ticket-header-navbar-green-rule.md) - ticket header navbar green rule
- [ticket-infolist-filament-v5-pattern](./ticket-infolist-filament-v5-pattern.md) - ticket infolist filament v5 pattern
- [ticket-location-json-architecture](./ticket-location-json-architecture.md) - ticket location json architecture
- [ticket-map-runtime-boundary-rule](./ticket-map-runtime-boundary-rule.md) - ticket map runtime boundary rule
- [ticket-privacy-parity-audit](./ticket-privacy-parity-audit.md) - ticket privacy parity audit
- [ticket-riepilogo-reference-gap-plan](./ticket-riepilogo-reference-gap-plan.md) - ticket riepilogo reference gap plan
- [ticket-runtime-asset-integrity](./ticket-runtime-asset-integrity.md) - ticket runtime asset integrity
- [ticket-subscribers-vs-comment-subscribers](./ticket-subscribers-vs-comment-subscribers.md) - Ticket subscribers vs comment notification subscribers
- [ticket-type-icon-fixcity-svg](./ticket-type-icon-fixcity-svg.md) - Icona tipologia ticket - una sola SVG in Fixcity
- [ticket-view-fo-enrichment-map-media-comments](./ticket-view-fo-enrichment-map-media-comments.md) - FO dettaglio ticket - mappa, foto, commenti
- [ticket-wizard-runtime-regression](./ticket-wizard-runtime-regression.md) - ticket wizard runtime regression 2026 04 23
- [ticket-wizard-steps-in-form-rule](./ticket-wizard-steps-in-form-rule.md) - Ticket Wizard Steps in TicketForm Rule
- [ticket-workflow-state-machine](./ticket-workflow-state-machine.md) - Macchina a stati ticket - canon PA vs drift codebase
- [ticketform-pattern-reference](./ticketform-pattern-reference.md) - ticketform pattern reference
- [ticketinfolist-pattern-reference](./ticketinfolist-pattern-reference.md) - ticketinfolist pattern reference
- [tickets-table-filament-v5-pattern](./tickets-table-filament-v5-pattern.md) - tickets table filament v5 pattern
- [user-model-architecture](./user-model-architecture.md) - Fixcity User Model Architecture
- [visual-parity-report](./visual-parity-report.md) - visual parity report
- [wizard-architecture-filament-theme-boundary](./wizard-architecture-filament-theme-boundary.md) - wizard architecture filament theme boundary
- [wizard-form-validation](./wizard-form-validation.md) - wizard form validation
- [wizard-map-visibility-fix](./wizard-map-visibility-fix.md) - wizard map visibility fix
- [wizard-nav-responsive-alignment](./wizard-nav-responsive-alignment.md) - wizard nav responsive alignment
- [wizard-riepilogo-parity-audit](./wizard-riepilogo-parity-audit.md) - wizard riepilogo parity audit
- [wizard-runtime-lessons-learned](./wizard-runtime-lessons-learned.md) - wizard runtime lessons learned
- [wizard-single-next-cta-rule](./wizard-single-next-cta-rule.md) - wizard single next cta rule
- [wizard-step-index-and-map-integration-rule](./wizard-step-index-and-map-integration-rule.md) - wizard step index and map integration rule
- [wizard-submit-modal-termini](./wizard-submit-modal-termini.md) - wizard submit modal termini
- [wizard-theme-integration](./wizard-theme-integration.md) - Wizard Theme Integration Contract
- [wizard-visual-parity](./wizard-visual-parity.md) - wizard visual parity
- [wizard-zen-philosophy](./wizard-zen-philosophy.md) - wizard zen philosophy
- [xotbasefield-view-rule](./xotbasefield-view-rule.md) - xotbasefield view rule
