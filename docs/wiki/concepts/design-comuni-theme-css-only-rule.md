# Design Comuni Theme CSS Only Rule

## Regola per Fixcity

Le view Fixcity del wizard segnalazione non devono contenere CSS inline per inseguire la parity Design Comuni.

## Owner corretti

- `Modules/Fixcity`: widget Filament, Livewire state, markup semantico, traduzioni, azioni.
- `Themes/Sixteen`: CSS Design Comuni, responsive, override Filament/Bootstrap Italia.

## Impatto sul wizard

`resources/views/filament/widgets/ticket-create-wizard.blade.php` deve esporre classi stabili come `ticket-wizard-root` e `segnalazione-wizard-root`; le regole su footer wizard, CTA, layout grid e nav sidebar si applicano dal CSS del tema.

## Build

Ogni modifica visuale collegata al tema richiede:

```bash
cd laravel/Themes/Sixteen
npm run build
npm run copy
```
