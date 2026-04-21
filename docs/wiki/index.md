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
| [header-green-branding-rule](./concepts/header-green-branding-rule.md) | concept | Token verdi vs parity kit: navbar chiara nel flusso segnalazione (vedi tema Sixteen) |
| [visual-parity-report](./concepts/visual-parity-report.md) | concept | Report parity visuale Design Comuni |
| [geo-unified-architecture](../../Geo/docs/wiki/concepts/coordinate-picker-architecture.md) | concept | Unificazione componenti mappa (MapPicker, CoordinatePicker) |

## Raw Sources

Vedi [[../raw/index|Lista Sorgenti Grezzi]]

## Index Globale

Vedi [[../../../../docs/wiki/index|Index Globale Wiki]]

---

*Ultimo aggiornamento: 2026-04-21*
