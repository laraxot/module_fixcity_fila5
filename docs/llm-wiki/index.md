---
title: "index"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-26
qmd: "index"
issues: []
discussions: []
status: draft
---

# Fixcity Module — Indice Documentazione

Modulo Laravel responsabile della gestione delle segnalazioni cittadine (Ticket).

---
## 🏛 Architettura e Governance

| Documento | Descrizione |
|-----------|-------------|
| [Profiles UUID contract](./concepts/profiles-uuid-contract.md) | Owner `profiles`: 1 migrazione Fixcity, `uuid` + `credits` nullable |
| **[Filament Wizard Pattern](../filament-wizard-pattern.md)** | **Guida principale** per wizard multi-step con Filament v5. |
| [Rules / Filament Wizard Rules](../rules/filament-wizard-rules.md) | Regole operative: docs-first, concorrenza agenti, Infolist vs Form Schema, anti-duplicazione. |
| [Wizard Governance Philosophy](../wizard-governance-philosophy.md) | Perché/regola/visione/zen: confini modulo-base-tema e anti-duplicazione. |
| [Structure](../structure.md) | Struttura moduli e directory. |
| [Module Boundary Philosophy](../MODULE-BOUNDARY-PHILOSOPHY.md) | **Zen dei confini tra moduli**: Geo possiede geolocation, Fixcity consuma. |

---

## 🧩 Widgets e Componenti

| Documento | Descrizione |
|-----------|-------------|
| **[CreateTicketWizardWidget](../CreateTicketWizardWidget.md)** | Widget Filament 3-step per creazione ticket: campi, metodi, traduzioni. |
| **[Filament Components Guidelines](../filament-components-guidelines.md)** | Guida corretta: Placeholder vs TextEntry, imports, anti-pattern verificati. |
| **[Wizard Visual Parity](../wizard-visual-parity.md)** | CSS scoped overrides per Bootstrap Italia parity. Entry point `app-test.css`. |
| [Wizard Single Next CTA Rule](./concepts/wizard-single-next-cta-rule.md) | Regola di UI parity: un solo tasto "Avanti" per step. |
| [Wizard Nav Responsive Alignment](./concepts/wizard-nav-responsive-alignment.md) | Analisi posizionamento CTA (Vertical stack + stretch per mobile). |
| [Livewire Cache Table Rule](./concepts/livewire-cache-table-rate-limiter.md) | Gestione backend cache per Rate Limiter e Checksum. |
| Segnalazione Runtime Asset Integrity (documento non presente in `concepts/`, da riscrivere o togliere) | Catena fix su 404 asset + bootstrap Livewire/Alpine coerente. |
| **[Select Enum Best Practices](../filament-select-enum-best-practices.md)** | Come usare correttamente Select con enum in Filament: evita codice complesso! |
| [AddressInput (Geo)](../../../Geo/docs/address-input-component.md) | Componente Filament per input indirizzi con mappa. |

---

## 📚 Altri Riferimenti

- [Traduzioni IT](../../lang/it/create_ticket_wizard.php) / [Traduzioni EN](../../lang/en/create_ticket_wizard.php)
- [Storie di Sviluppo](../stories/index.md)
- [PHPStan Fixes](../phpstan-fix-plan.md)
- [Sixteen Theme Documentation](../../../../Themes/Sixteen/docs/00-INDEX.md)

---

## 🧘 Xot Base Patterns (Global)

- XotBaseWizardWidget Philosophy (documento non presente in `Modules/Xot/docs`)
- Infolists for Summary (documento non presente in `Modules/Xot/docs`)
- [Schemas Unified Religion](../../../../../docs/schemas-unified-religion.md)
- AutoLabel Guidelines (documento non presente in `Modules/UI/docs`)
