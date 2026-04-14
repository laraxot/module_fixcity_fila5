# wizard geolocation loading feedback

## contesto

Nel wizard di creazione segnalazione, il click su "usa la tua posizione" avvia due operazioni asincrone:

1. geolocalizzazione browser (`navigator.geolocation`)
2. reverse geocoding remoto (nominatim)

Senza feedback visivo, l'utente non distingue tra "click non preso" e "operazione in corso".

## perche

- **business**: riduce click ripetuti e abbandoni nel passaggio indirizzo
- **ux**: rende esplicito che il sistema sta lavorando
- **accessibilita**: `aria-busy` e `role="status"` comunicano lo stato ai lettori assistivi
- **robustezza**: evita conflitti su pagine con piu componenti geolocalizzazione

## decisione

- aggiungere stato `loading` sul link "usa la tua posizione"
- mostrare spinner + testo localizzato durante l'operazione
- disabilitare temporaneamente il trigger per prevenire doppio click
- usare `this.$wire.set(...)` invece di risoluzione globale via `document.querySelector('[wire:id]')`

## file toccati

- `laravel/Modules/Geo/resources/views/filament/forms/components/address-input.blade.php`
- `laravel/Modules/Geo/lang/it/address.php`
- `laravel/Modules/Geo/lang/en/address.php`
- `laravel/Modules/Geo/lang/de/address.php`

## chiavi traduzione introdotte

- `geo::address.geolocation.locating`
- `geo::address.geolocation.timeout`
- `geo::address.geolocation.unavailable`

## regola zen

Quando un'azione utente richiede I/O o permessi, l'interfaccia deve mostrare stato, non silenzio.

## collegamenti

- [stories index](./index.md)
- [create ticket wizard](../CreateTicketWizardWidget.md)
- [geo addressinput component](../../../Geo/docs/address-input-component.md)
