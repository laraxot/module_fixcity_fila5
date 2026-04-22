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
| [profiles-uuid-contract](./concepts/profiles-uuid-contract.md) | concept | Contratto schema `profiles`: `id` intero + `uuid` separato nella migrazione owner |
| [wizard-single-next-cta-rule](./concepts/wizard-single-next-cta-rule.md) | concept | Wizard segnalazione: CTA primaria unica `Avanti` |
| [wizard-nav-responsive-alignment](./concepts/wizard-nav-responsive-alignment.md) | concept | Allineamento responsivo navigazione wizard |
| [segnalazione-privacy-parity-audit](./concepts/segnalazione-privacy-parity-audit.md) | concept | Audit parity visuale step privacy (colori header, CTA, responsive) |
| [segnalazione-runtime-asset-integrity](./concepts/segnalazione-runtime-asset-integrity.md) | concept | Integrità asset runtime (404 CSS/JS, Livewire/Alpine bootstrap coerente) |
| [livewire-cache-table-rate-limiter](./concepts/livewire-cache-table-rate-limiter.md) | concept | Fix QueryException su `cache` mancante durante update Livewire |
| [context-compression-and-summary-infolist-rule](./concepts/context-compression-and-summary-infolist-rule.md) | concept | Regola permanente: summary wizard via Infolist entries, niente `SchemaView`; disciplina context compression |
| [theme-owned-wizard-css-parity-rule](./concepts/theme-owned-wizard-css-parity-rule.md) | concept | Il Blade wizard Fixcity resta markup-only; CSS parity nel tema Sixteen con build/copy |
| [header-green-branding-rule](./concepts/header-green-branding-rule.md) | concept | Token verdi vs parity kit: navbar chiara nel flusso segnalazione (vedi tema Sixteen) |
| [visual-parity-report](./concepts/visual-parity-report.md) | concept | Report parity visuale Design Comuni |
| [geo-unified-architecture](../../Geo/docs/wiki/concepts/coordinate-picker-architecture.md) | concept | Unificazione componenti mappa (MapPicker, CoordinatePicker) |
| [wizard-summary-infolist-runtime-fix-2026-04-22](../stories/wizard-summary-infolist-runtime-fix-2026-04-22.md) | story | Fix runtime: summary wizard con entry Infolist e senza `SchemaView` |
| [context-compression-plugin-runtime](../context-compression-plugin-runtime.md) | source | Regola operativa Fixcity per evitare overflow contesto e distinzione plugin OpenRouter/context-mode |
| [filament5-schema-namespaces-and-wizard-summary](./concepts/filament5-schema-namespaces-and-wizard-summary.md) | concept | Namespace Filament 5.x corretti + pattern TextEntry+Get per getSummarySchema + linter alias anti-pattern |
| [filament5-schema-section-namespace-rule](./concepts/filament5-schema-section-namespace-rule.md) | concept | `Section` viene da Schemas, non da Infolists; entries read-only restano Infolists |
| [design-comuni-theme-css-only-rule](./concepts/design-comuni-theme-css-only-rule.md) | concept | Fixcity espone markup/stato; Sixteen possiede CSS parity, niente `<style>` nel widget wizard |
| [filament-multiple-forms](./concepts/filament-multiple-forms.md) | concept | Documentazione su Filament Multiple Forms
| [context-compression-discipline](../../../../docs/wiki/concepts/context-compression-discipline.md) | concept | Recupero docs/story con context-mode + QMD per evitare errore BMAD 131k token |

## Raw Sources

Vedi [[../raw/index|Lista Sorgenti Grezzi]]

## Index Globale

Vedi [[../../../../docs/wiki/index|Index Globale Wiki]]

---

*Ultimo aggiornamento: 2026-04-22*
