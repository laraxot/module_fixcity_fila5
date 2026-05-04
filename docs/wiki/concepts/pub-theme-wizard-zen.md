# PubThemeWizard — Filosofia e Implementazione

## Il Problema Iniziale

Filament definisce il Wizard in due parti:
1. **PHP Component** (`Wizard.php`) — logica, step, azioni
2. **Blade Template** (`wizard.blade.php`) — markup, Alpine.js, presentazione

Il problema: dove personalizzare il "vestito" (presentazione) del wizard per Design Comuni?

## Le Soluzioni Errate (Non Fare Queste)

### ❌ Modifica in `CreateTicketWizardWidget`
```php
// ERRORE: Il Widget non deve definire la vista
class CreateTicketWizardWidget extends XotBaseWidget
{
    // Non mettere logica vista qui
}
```

### ❌ Modifica diretta in `TicketForm::getFormSchema()`
```php
// ERRORE: Logica condizionale nel Blueprint
public static function getFormSchema(): array
{
    $wizard = Wizard::make(static::getWizardSteps())
        ->skippable()
        ->persistStepInQueryString();
    
    // NON fare controllo inAdmin() qui - viola la separazione
    if (!inAdmin()) {
        $wizard->view('pub_theme::components.wizard');
    }
    
    return [$wizard];
}
```

Perché è sbagliato? Perché `TicketForm` è un **Blueprint** (Schema) e non deve contenere logica di presentazione runtime.

## La Soluzione Corretta (Zen)

> "Il Blueprint definisce CHE vista usare, non SE usarla."

### ✅ Creare `PubThemeWizard` che estende `Wizard`

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

### ✅ Usare `PubThemeWizard` nel Blueprint

```php
// Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php
public static function getFormSchema(): array
{
    // Zen: Blueprint definisce il vestito una volta
    $wizard = PubThemeWizard::make(static::getWizardSteps())
        ->skippable()
        ->persistStepInQueryString();
    
    return [
        $wizard,
    ];
}
```

### ✅ Creare la vista tema in `pub_theme::components.wizard`

```blade
{{-- Themes/Sixteen/resources/views/components/wizard.blade.php --}}
{{-- 
    Philosophy (Zen):
    - Filament Wizard PHP = Logic (Wizard.php) — NOT touched
    - This Blade = "Vestito" (Presentation layer) — customized here
    - Follows Design Comuni stepper pattern
    
    Reference: filament-schemas::components.wizard (original)
    Override: pub_theme::components.wizard
--}}

@php
    $isContained = $isContained();
    $key = $getKey();
    // ... resto della logica Alpine.js copiata da Filament official
@endphp

{{-- Markup personalizzato per Design Comuni --}}
<div x-data="wizardSchemaComponent({...})">
    {{-- Header con classi Design Comuni --}}
    <ol class="steppers">...</ol>
    
    {{-- Step content --}}
    @foreach ($steps as $step)
        <div x-show="step === @js($step->getKey())">
            {{ $step }}
        </div>
    @endforeach
    
    {{-- Footer actions --}}
</div>
```

## Perché Questa Architettura?

### 1. **Separazione delle Responsabilità**
- **Module (Fixcity)**: Logica di business, definizione step, validazione
- **Theme (Sixteen)**: Presentazione, CSS, markup Design Comuni

### 2. **Blueprint vs Runtime**
- `TicketForm::getFormSchema()` è un **Blueprint** — dice ALLA FINE che componenti usare
- Non deve contenere `if (!inAdmin())` — quella è logica runtime
- Il Blueprint dice: "Usa `PubThemeWizard`" (che ha già la vista tema)

### 3. **Estensibilità**
- Admin panel: può usare `Wizard` normale (vista default Filament)
- Frontoffice: usa `PubThemeWizard` (vista personalizzata)
- Nessuna logica condizionale sparsa nel codice

## File Coinvolti

### Module (Fixcity)
- `app/Filament/Schemas/Components/PubThemeWizard.php` — La classe che definisce la vista
- `app/Filament/Resources/TicketResource/Schemas/TicketForm.php` — Usa `PubThemeWizard`
- `docs/wiki/concepts/pub-theme-wizard-zen.md` — Questa documentazione

### Theme (Sixteen)
- `resources/views/components/wizard.blade.php` — La vista personalizzata
- `docs/wiki/concepts/filament-wizard-theme-override.md` — Filosofia override
- `docs/wiki/concepts/zen-wizard-header-philosophy.md` — Filosofia header

## Come Funziona il Render

1. `TicketForm::getFormSchema()` restituisce `PubThemeWizard`
2. `PubThemeWizard` ha `$view = 'pub_theme::components.wizard'`
3. Laravel risolve `pub_theme::components.wizard` → `Themes/Sixteen/resources/views/components/wizard.blade.php`
4. La vista Blade usa `wizardSchemaComponent` Alpine.js (caricato da Filament)
5. Il markup è personalizzato per Design Comuni, ma la logica è quella ufficiale Filament

## Quality Checks

Dopo ogni modifica:
```bash
# PHP syntax
php -l laravel/Modules/Fixcity/app/Filament/Schemas/Components/PubThemeWizard.php
php -l laravel/Themes/Sixteen/resources/views/components/wizard.blade.php

# PHPStan
cd laravel && ./vendor/bin/phpstan analyse Modules/Fixcity/app/Filament/Schemas/Components/PubThemeWizard.php

# Pint
./vendor/bin/pint --dirty --format agent

# Clear caches
php artisan view:clear
```

## Riferimenti

- Filament 5.x Wizard Source: https://github.com/filamentphp/filament/blob/5.x/packages/schemas/src/Components/Wizard.php
- Filament 5.x Wizard Blade: https://github.com/filamentphp/filament/blob/5.x/packages/schemas/resources/views/components/wizard.blade.php
- Design Comuni Stepper: https://designers.italia.it/pattern/stepper/
- BMAD Story 8-106: Header navigation JSON-driven + segnalazione-crea parity
