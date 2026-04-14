# Fixcity Module — Indice Documentazione

Modulo Laravel responsabile della gestione delle segnalazioni cittadine (Ticket).

---

## 🏛 Architettura e Governance

| Documento | Descrizione |
|-----------|-------------|
| **[Filament Wizard Pattern](./filament-wizard-pattern.md)** | **Guida principale** per wizard multi-step con Filament v5. |
| [Rules / Filament Wizard Rules](./rules/filament-wizard-rules.md) | Regole operative: docs-first, concorrenza agenti, Infolist vs Form Schema, anti-duplicazione. |
| [Wizard Governance Philosophy](./wizard-governance-philosophy.md) | Perché/regola/visione/zen: confini modulo-base-tema e anti-duplicazione. |
| [Structure](./structure.md) | Struttura moduli e directory. |
| [Module Boundary Philosophy](./MODULE-BOUNDARY-PHILOSOPHY.md) | **Zen dei confini tra moduli**: Geo possiede geolocation, Fixcity consuma. |

---

## 🧩 Widgets e Componenti

| Documento | Descrizione |
|-----------|-------------|
| **[CreateTicketWizardWidget](./CreateTicketWizardWidget.md)** | Widget Filament 3-step per creazione ticket: campi, metodi, traduzioni. |
| **[Filament Components Guidelines](./filament-components-guidelines.md)** | Guida corretta: Placeholder vs TextEntry, imports, anti-pattern verificati. |
| **[Wizard Visual Parity](./wizard-visual-parity.md)** | CSS scoped overrides per Bootstrap Italia parity. Entry point `app-test.css`. |
| **[Select Enum Best Practices](./filament-select-enum-best-practices.md)** | Come usare correttamente Select con enum in Filament: evita codice complesso! |
| [AddressInput (Geo)](../../Geo/docs/address-input-component.md) | Componente Filament per input indirizzi con mappa. |

---

## 📚 Altri Riferimenti

- [Traduzioni IT](../lang/it/create_ticket_wizard.php) / [Traduzioni EN](../lang/en/create_ticket_wizard.php)
- [Storie di Sviluppo](./stories/index.md)
- [PHPStan Fixes](./phpstan/index.md)
- [Sixteen Theme Documentation](../../Themes/Sixteen/docs/00-index.md)

---

## 🧘 Xot Base Patterns (Global)

- [XotBaseWizardWidget Philosophy](../../Xot/docs/filament/widgets/xot-base-wizard-widget-philosophy.md)
- [Infolists for Summary](../../Xot/docs/filament/widgets/infolists-for-summary.md)
- [Schemas Unified Religion](../../../../docs/schemas-unified-religion.md)
- [AutoLabel Guidelines](../../UI/docs/autolabel-guidelines.md)
