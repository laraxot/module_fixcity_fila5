# PubThemeWizard — Filosofia e Implementazione

## Il Problema Iniziale

Filament definisce il Wizard in due parti:

1. **PHP Component** (`Wizard.php`) — logica, step, azioni
2. **Blade Template** (`wizard.blade.php`) — markup, Alpine.js, presentazione

Il problema: dove applicare il «vestito» Design Comuni **solo** al frontoffice cittadino, senza forzare la vista `pub_theme` dentro il **pannello admin** Filament?

## Errore da Evitare

### `PubThemeWizard` ovunque (incluso admin)

Usare sempre `PubThemeWizard::make($steps)` anche quando il form gira nel backoffice Filament **è sbagliato**: il pannello deve restare sulla skin standard Filament; la vista `pub_theme::components.wizard` è per il sito pubblico con `pub_theme`, non per l’admin.

## Soluzione Corretta (Zen)

> **Admin** = `Filament\Schemas\Components\Wizard` (skin pannello).  
> **Frontoffice** = `PubThemeWizard` (skin tema pubblico).

### Componente `PubThemeWizard`

```php
// Modules/Fixcity/app/Filament/Schemas/Components/PubThemeWizard.php
final class PubThemeWizard extends Wizard
{
    /**
     * @var view-string
     */
    protected string $view = 'pub_theme::components.wizard';
}
```

Solo contesti **non admin** (o widget esplicitamente frontoffice) devono istanziare `PubThemeWizard`.

### Blueprint `TicketForm` — ramo per contesto

Il blueprint può e deve distinguere **contesto pannello vs pubblico** con `inAdmin()` (o equivalente centralizzato):

```php
// Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php
public static function getFormSchema(): array
{
    $steps = static::getWizardSteps();

    $wizard = inAdmin()
        ? \Filament\Schemas\Components\Wizard::make($steps)
            ->skippable()
            ->persistStepInQueryString()
        : PubThemeWizard::make($steps)
            ->skippable()
            ->persistStepInQueryString();

    return [$wizard];
}
```

Non si tratta di «logica di presentazione sparsa»: è il **confine** tra due superfici UX (Filament standard vs tema pubblico), documentato in [filament-admin-pub-theme-wizard-boundary](../../../../docs/wiki/concepts/filament-admin-pub-theme-wizard-boundary.md).

### Vista tema `pub_theme::components.wizard`

Markup e classi Design Comuni vivono nel tema (es. Sixteen), non in `<style>` inline nei Blade del modulo.

## Separazione delle Responsabilità

| Livello | Ruolo |
|--------|--------|
| **Modulo Fixcity** | Step, validazione, stato form, `PubThemeWizard` come tipo dedicato al pub_theme |
| **Tema (Sixteen)** | CSS, asset, Blade `components/wizard` per il frontoffice |
| **Admin Filament** | `Wizard` nativo, nessun override `pub_theme` |

## File Coinvolti

- `app/Filament/Schemas/Components/PubThemeWizard.php` — wizard con vista tema (solo uso appropriato)
- `app/Filament/Resources/TicketResource/Schemas/TicketForm.php` — `inAdmin()` → `Wizard` / altrimenti `PubThemeWizard`
- `app/Filament/Widgets/CreateTicketWizardWidget.php` — entrypoint frontoffice; `PubThemeWizard` coerente con la regola
- [ticket-wizard-frontoffice.md](../../ticket-wizard-frontoffice.md) — regola operativa e anti-pattern

## Riferimenti

- [filament-admin-pub-theme-wizard-boundary](../../../../docs/wiki/concepts/filament-admin-pub-theme-wizard-boundary.md)
- [theme-owned-wizard-css-parity-rule](./theme-owned-wizard-css-parity-rule.md)
- Filament Wizard: pacchetto `schemas` Filament 5.x
- Design Comuni Stepper: https://designers.italia.it/pattern/stepper/

*Ultimo aggiornamento: 2026-05-04*
