---
name: wizard-submit-modal-termini
description: Modal "Termini e condizioni" prima del submit wizard — parity segnalazione-03-riepilogo.html
type: concept
---

# Wizard Submit — Modal Termini e Condizioni

## Pattern

Il tasto "Invia" nello step riepilogo NON chiama direttamente `wire:click="submit"`.
Apre prima un modal Bootstrap Italia con i termini e condizioni; la conferma chiama submit.

```blade
{{-- Tasto Invia — apre modal --}}
<button type="button" class="btn btn-primary btn-sm fw-bold flex-fill"
    data-bs-toggle="modal" data-bs-target="#modal-termini">
    {{ __('fixcity::segnalazione.actions.submit.label') }}
</button>

{{-- Modal --}}
<div class="modal fade" id="modal-termini" tabindex="-1" ...>
    ...
    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" wire:click="submit">
        {{ __('fixcity::segnalazione.actions.confirm_submit.label') }}
    </button>
</div>
```

## Chiavi traduzione

| Chiave | IT |
|--------|-----|
| `fixcity::segnalazione.actions.submit.label` | Invia |
| `fixcity::segnalazione.actions.confirm_submit.label` | Conferma e invia |
| `fixcity::segnalazione.actions.cancel.label` | Annulla |
| `fixcity::segnalazione.actions.close.label` | Chiudi |
| `fixcity::segnalazione.modal.terms.title` | Termini e condizioni |
| `fixcity::segnalazione.modal.terms.body` | testo dichiarazione |

## Riferimento Design Comuni

`segnalazione-03-riepilogo.html` — tasto "Invia" → modal → "Conferma e invia"

## File

- Blade: `Modules/Fixcity/resources/views/filament/widgets/ticket-create-wizard.blade.php`
- Lang: `Modules/Fixcity/lang/it/segnalazione.php`
