# Wiki Locale - Modulo Fixcity

## Schema di Riferimento

Vedi [[../../../../docs/.schema/WIKI_SCHEMA.md|Schema Wiki Globale]]

## Struttura Locale

```
wiki/
├── concepts/        # Pattern e metodologie
├── entities/        # Classi e componenti
├── overviews/       # Panoramiche modulo
├── sources/         # Riepiloghi sorgenti
├── comparisons/     # Confronti
├── decisions/       # Decisioni architetturali
├── troubleshooting/ # Risoluzione problemi
├── _archive/        # Archivio
└── _templates/      # Template
```

## Pagine Compilate

| Pagina | Tipo | Argomento |
|--------|------|-----------|
| [fixcity-module](./overviews/fixcity-module.md) | overview | Ticket system, wizard frontoffice cittadini, pannello operatori |
| [segnalazione-crea-step-dati-screenshot-audit-2026-04-28](./comparisons/segnalazione-crea-step-dati-screenshot-audit-2026-04-28.md) | comparison | Audit screenshot runtime step `Dati della segnalazione`: box laterale vuoto, search clipping, mappa con testo grezzo sovrapposto |
| [admin-ticket-create-map-visual-contract](./concepts/admin-ticket-create-map-visual-contract.md) | concept | Contratto mappa route admin `fixcity/admin/tickets/create` e boundary runtime |
| [map-component-purpose-architecture](../../Geo/docs/wiki/concepts/map-component-purpose-architecture.md) | concept | Scopo business mappa admin ticket wizard + architettura Geo/Fixcity/Sixteen | 2026-04-27 |
| [location-capture-map-wizard](./concepts/location-capture-map-wizard.md) | concept | Scopo business e utilita' operativa della mappa nel wizard ticket |
| [segnalazione-crea-map-fullscreen-contract](./concepts/segnalazione-crea-map-fullscreen-contract.md) | concept | Contratto fullscreen mappa nello step dati; story 8-74 refinement |
| [segnalazione-crea-map-fullscreen-refinement](../stories/segnalazione-crea-map-fullscreen-refinement.md) | story | Story module-side per migliorare fullscreen mappa sull'URL con `step=` |
| [ticket-location-not-saved-mass-assignment](./troubleshooting/ticket-location-not-saved-mass-assignment.md) | troubleshooting | Location non persistita: ROOT CAUSE `dehydrated(false)` nel trait + mutator mancante (fix: story 8-65, supersede 8-64) | 2026-04-28 |
| [filament5-entangle-contract](../../Geo/docs/wiki/concepts/filament5-custom-field-entangle-contract.md) | concept | Perché `$applyStateBindingModifiers` è OBBLIGATORIO per entangle — prova vendor Filament 5 | 2026-04-28 |
| [admin-map-asset-registry-mismatch](./concepts/admin-map-asset-registry-mismatch.md) | concept | mismatch tra asset registry Filament e file effettivamente deployati |
| [obsidian-skills-and-ingest-checklist](./concepts/obsidian-skills-and-ingest-checklist.md) | concept | Checklist continua su Obsidian, skills, ingest e allineamento docs |
| [segnalazioni-elenco-map-architecture](./concepts/segnalazioni-elenco-map-architecture.md) | concept | Architettura pagina elenco segnalazioni: Lit map + GeoJSON statico (farmshops pattern) + filtri TicketTypeEnum | 2026-04-29 |
| [frontoffice-no-standalone-livewire](./concepts/frontoffice-no-standalone-livewire.md) | concept | No Livewire puro fuori widget Filament; `/it` via CMS `segnalazioni-layout` | 2026-05-28 |
| [filament-first-ui-boundary](./concepts/filament-first-ui-boundary.md) | concept | Filament-first: tab/UI FO vs admin ticket; link STORY-065 | 2026-05-28 |
| [second-brain-session-bootstrap](../../../../docs/wiki/concepts/second-brain-session-bootstrap.md) | concept | Bootstrap always-on: verifica stack + update + smoke search root/moduli prima del lavoro applicativo |
| [profiles-uuid-contract](./concepts/profiles-uuid-contract.md) | concept | Contratto schema `profiles`: `id` intero + `uuid` separato nella migrazione owner |
| [wizard-single-next-cta-rule](./concepts/wizard-single-next-cta-rule.md) | concept | Wizard segnalazione: CTA primaria unica `Avanti` |
| [wizard-nav-responsive-alignment](./concepts/wizard-nav-responsive-alignment.md) | concept | Allineamento responsivo navigazione wizard |
| [segnalazione-privacy-parity-audit](./concepts/segnalazione-privacy-parity-audit.md) | concept | Audit parity visuale step privacy (colori header, CTA, responsive) |
| [segnalazione-runtime-asset-integrity](./concepts/segnalazione-runtime-asset-integrity.md) | concept | Integrità asset runtime (404 CSS/JS, Livewire/Alpine bootstrap coerente) |
| [livewire-cache-table-rate-limiter](./concepts/livewire-cache-table-rate-limiter.md) | concept | Fix QueryException su `cache` mancante durante update Livewire |
| [context-compression-and-summary-infolist-rule](./concepts/context-compression-and-summary-infolist-rule.md) | concept | Regola permanente: summary wizard via Infolist entries, niente `SchemaView`; disciplina context compression |
| [wizard-architecture-filament-theme-boundary](./concepts/wizard-architecture-filament-theme-boundary.md) | concept | Filament wizard theme boundary - module markup, theme CSS, no logic duplication | 2026-05-04 |
| [filament-admin-pub-theme-wizard-boundary](../../../../docs/wiki/concepts/filament-admin-pub-theme-wizard-boundary.md) | concept | Admin: `Wizard` Filament; frontoffice: `PubThemeWizard`; vedi `TicketForm::getFormSchema()` | 2026-05-04 |
| [header-green-branding-rule](./concepts/header-green-branding-rule.md) | concept | Token verdi vs parity kit: navbar chiara nel flusso segnalazione (vedi tema Sixteen) |
| [visual-parity-report](./concepts/visual-parity-report.md) | concept | Report parity visuale Design Comuni |
| [segnalazione-crea-step1-diff-2026-05-04](./concepts/segnalazione-crea-step1-diff-2026-05-04.md) | concept | Diff visivo Playwright 2026-05-04: stepper, bottone, checkbox, bottoni spurii — story 7-77 | 2026-05-04 |
| [segnalazione-design-comuni-comparison](./concepts/segnalazione-design-comuni-comparison.md) | comparison | Full diff matrix: Fixcity vs Design Comuni reference (privacy, dati, riepilogo, conferma, elenco) | 2026-05-04 |
| [segnalazione-visual-parity-correction-plan](../../../../Themes/Sixteen/docs/wiki/concepts/segnalazione-visual-parity-correction-plan.md) | decision | Detailed fix plan: Bootstrap→Tailwind mapping, elenco layout, stepper labels, CTAs | 2026-05-04 |
| [segnalazione-01-privacy-design-comuni-vs-local-wizard](./comparisons/segnalazione-01-privacy-design-comuni-vs-local-wizard.md) | comparison | Step 1 privacy: reference [Design Comuni statiche](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html) vs wizard locale; HTML parity, no `<style>` in Blade, asset tema + `npm run build && npm run copy` | 2026-05-04 |
| [geo-unified-architecture](../../Geo/docs/wiki/concepts/coordinate-picker-architecture.md) | concept | Unificazione componenti mappa (MapPicker, CoordinatePicker) |
| [wizard-summary-infolist-runtime-fix-2026-04-22](../stories/wizard-summary-infolist-runtime-fix-2026-04-22.md) | story | Fix runtime: summary wizard con entry Infolist e senza `SchemaView` |
| [wizard-map-runtime-asset-chain](../stories/wizard-map-runtime-asset-chain.md) | story | Analisi catena asset runtime della mappa su admin ticket create |
| [context-compression-plugin-runtime](../context-compression-plugin-runtime.md) | source | Regola operativa Fixcity per evitare overflow contesto e distinzione plugin OpenRouter/context-mode |
| [filament5-schema-namespaces-and-wizard-summary](./concepts/filament5-schema-namespaces-and-wizard-summary.md) | concept | Namespace Filament 5.x corretti + pattern TextEntry+Get per getSummarySchema + linter alias anti-pattern |
| [ticketform-pattern-reference](./concepts/ticketform-pattern-reference.md) | concept | TicketForm pattern di riferimento - XotBaseResourceForm + LangServiceProvider + Wizard + Infolist entries | 2026-05-05 |
| [ticketinfolist-pattern-reference](./concepts/ticketinfolist-pattern-reference.md) | concept | TicketInfolist pattern di riferimento - XotBaseResourceInfolist + Infolist entries | 2026-05-05 |
| [ticket-infolist-filament-v5-pattern](./concepts/ticket-infolist-filament-v5-pattern.md) | concept | TicketInfolist Filament v5 Hybrid Pattern - configure() + getInfolistSchema() dual API | 2026-05-05 |
| [tickets-table-filament-v5-pattern](./concepts/tickets-table-filament-v5-pattern.md) | concept | TicketsTable Filament v5 Hybrid Pattern - configure() + table() dual API | 2026-05-05 |
| [ux-design-fixcity](../../../../docs/ux-design-fixcity.md) | concept | Specifiche UX canoniche (stack no bootstrap, form=filament, boundary body/dress, gates) |  |

## Raw Sources

Vedi [[../raw/index|Lista Sorgenti Grezzi]]

## Index Globale

Vedi [[../../../../docs/wiki/index|Index Globale Wiki]]

---

*Ultimo aggiornamento: 2026-05-04*

- [context overflow compression rule](./concepts/context-overflow-compression-rule.md): workflow Fixcity anti overflow contesto BMAD
- [fix complete only after target route recheck](./concepts/fix-complete-only-after-target-route-recheck.md): un fix Fixcity e' chiuso solo dopo verifica della URL finale reale con step/query corretti
- [phpstan-runtime-priority-rule](./concepts/phpstan-runtime-priority-rule.md): su `segnalazione-crea` il ripristino runtime verificato ha priorita' sul cleanup statico diffuso
- [wizard-runtime-lessons-learned](./concepts/wizard-runtime-lessons-learned.md): lezioni da merge conflict, EnumSelect signature fix, e namespace CoordinatePicker
- [visual-parity-verification-rule](../../../Themes/Sixteen/docs/wiki/concepts/visual-parity-verification-rule.md): dopo ogni modifica — verificare nel browser all'URL di riferimento, mai dichiarare fix completo senza verifica visuale

| [segnalazione-bootstrap-tailwind-conversion](./concepts/segnalazione-bootstrap-tailwind-conversion.md) | concept | Conversione completata Bootstrap→Tailwind per 6 pagine Design Comuni (01-04, area-personale, elenco) | 2026-05-04 |
| [ticketinfolist-pattern-reference](./concepts/ticketinfolist-pattern-reference.md) | concept | Pattern Filament `Schemas/<Model>Infolist` applicato a `TicketResource` con estensione `XotBaseResourceInfolist` | 2026-05-05 |
