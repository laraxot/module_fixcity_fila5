# Story: controlli mappa wizard segnalazione

## Contesto

Nel flusso Filament/Livewire (`CreateTicketWizardWidget`) la posizione della segnalazione è definita da coordinate WGS84 su `Ticket`; la mappa è fornita dal modulo **Geo** (`LeafletMarkerMapInput`) e non deve duplicare logica dominio.

## Problema

Sulla pagina test `tests/segnalazione-crea` (step dati) serviva:

- pulsante **schermo intero** per usare la mappa comodamente;
- pulsante **centra sulla posizione corrente** ben visibile e funzionante;
- **latitude** / **longitude** aggiornati nello stato Livewire dopo geolocalizzazione, click e drag.

In più, con wizard a step, la mappa può inizializzarsi a dimensione zero se lo step non è visibile: serve `invalidateSize()` quando il contenitore diventa visibile o dopo fullscreen.

## Soluzione implementata

- Controlli in **overlay** (angolo in alto a destra) dentro il blocco `wire:ignore` con la mappa, così restano sempre sul layer corretto.
- Fullscreen sulla **shell** della mappa (`requestFullscreen` + fallback webkit/ms), con `invalidateSize()` sugli eventi fullscreen.
- Se `boot()` trova già `el._geoLeafletMap`, esegue solo `invalidateSize()` (fix navigazione Livewire / rientro sullo step).
- `IntersectionObserver` sul contenitore per ridisegnare quando la mappa entra in viewport.

## Riferimento esterno (perché documentare)

Il [gist Karpathy su LLM wiki](https://gist.github.com/karpathy/442a6bf555914893e9891c11519de94f) descrive una **base di conoscenza** in Markdown aggiornata dagli agenti: in questo progetto il ruolo analogo è la documentazione nei `docs` dei moduli (Geo per la mappa, Fixcity per il wizard), non il codice inline sparso.

## File

- `laravel/Modules/Geo/resources/views/filament/forms/components/leaflet-marker-map-input.blade.php`
- `laravel/Modules/Geo/lang/{it,en}/leaflet_map.php`

Documentazione modulo: [Geo — Filament forms components](../../../Geo/docs/filament-forms-components.md).

## Verifica manuale

1. Aprire `/it/tests/segnalazione-crea` e andare allo step con la mappa.
2. Cliccare fullscreen: la mappa deve riempire lo schermo e restare utilizzabile.
3. Cliccare posizione corrente: zoom, marker e campi nascosti lat/lng devono allinearsi.
4. Cambiare step e tornare: la mappa non deve restare “schiacciata” o vuota.
