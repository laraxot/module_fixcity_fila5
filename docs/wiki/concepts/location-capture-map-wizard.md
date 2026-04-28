# Ticket location capture map workflow

## Scopo business
La mappa nello step `data` del wizard non e' un elemento grafico opzionale: e' il meccanismo con cui il cittadino indica **dove** avviene il disservizio, cosi il Comune puo':

- classificare e smistare la segnalazione al team territoriale corretto;
- ridurre tempi e costi di sopralluogo;
- evitare ticket ambigui con descrizioni solo testuali;
- consolidare dati geografici utili per priorita' e pianificazione.

In sintesi: la mappa aumenta qualita' del dato, velocizza la presa in carico e rende misurabile il fenomeno sul territorio.

## Purpose tecnico
Capture the reporter's coordinates and related address metadata to populate `location` and hydrate `latitude`/`longitude` before persistence.

## Flusso operativo
1. **Wizard step “data”** loads a `CoordinatePicker` component.
2. Component renders an embedded Leaflet map.
3. If latitude/longitude are `null`, the map attempts to geolocate the user.
4. User can also manually pick a point or search an address.
5. Selected coordinates are stored in the `location` field (or `location1` in legacy schemas).

## Components
- **CoordinatePicker** (`laravel/Modules/Geo/resources/js/components/coordinate-picker-lit.js`)
- **MapPicker** (`laravel/Modules/Geo/resources/js/components/map-picker-lit.js`)
- Both use `createMapPickerLeafletIcon` for custom marker SVG.
- Geolocation guarded by `_geolocated` flag to avoid double requests.

## Perche' serve nel dominio Fixcity
- **Precisione**: marker e coordinate numeriche riducono interpretazioni soggettive.
- **Operativita'**: squadre tecniche ricevono un punto operativo, non solo un testo.
- **Prioritizzazione**: cluster geografici aiutano a identificare aree critiche.
- **Tracciabilita'**: il riepilogo mostra indirizzo o lat/lng, migliorando audit e verifica.

## Validation
- Coordinates are parsed to 6‑decimal precision (`toFixed(6)`).
- Non‑finite values abort update and reset flag.
- On successful pick, `coords-changed` event bubbles up to Livewire.
- Backend validation ensures `latitude` and `longitude` are numeric before save.

## Boundary modulo/tema
- **Fixcity** e' owner del significato business del campo location (wizard, submit, summary).
- **Geo** e' owner del runtime map picker (Leaflet, geolocation, reverse geocoding).
- **Sixteen** governa parity visuale/frontoffice; non ridefinisce la logica dominio del campo.

## Testing
- **Feature tests** verify route `/admin/tickets/create` redirects to login when unauthenticated.
- **Livewire tests** assert that the wizard step renders the coordinate picker and captures `coords-changed`.
- **Playwright screenshots** confirm map renders correctly in fullscreen and after navigation.
- **Screenshots** stored in `bashscripts/debug_map_500.png` for regression checks.

## Documentation Updates
- Add reference entry in `docs/wiki/concepts/llm-wiki-operational-discipline.md`.
- Log entry in `docs/wiki/log.md` when this workflow changes.
- Ensure `docs/wiki/index.md` links to this page.

## Checklist for Future Changes
- [ ] Update `CoordinatePicker` guard logic if new geolocation API changes.
- [ ] Verify marker SVG assets remain in `laravel/Modules/Geo/resources/svg/`.
- [ ] Run `phpstan analyse` on `CoordinatePicker` with max level.
- [ ] Run `phpmd` with `unusedcode,design` rules.
- [ ] Execute `phpinsights analyse` on the Fixcity module.
- [ ] Add/Update functional tests covering geolocation fallback paths.
- [ ] Review visual parity with reference `graduatoria-area-personale.html`.

*Last updated: 2026-04-27*