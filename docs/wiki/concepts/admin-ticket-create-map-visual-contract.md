# Admin ticket create map visual contract

## Contesto

Route target: `fixcity/admin/tickets/create` (panel Filament admin).

Questa route non va confusa con i flussi frontoffice `tests/segnalazione-crea`.

## Contratto visuale minimo

- la mappa e' visibile con altezza stabile
- i tile Leaflet sono completi (niente quadrati grigi persistenti)
- controlli zoom/fullscreen/layer (se previsti dal picker) sono usabili
- marker/center sono coerenti con stato campo `location`

## Boundary tecnico

- owner form: `TicketResource/Schemas/TicketForm`
- owner resource routing: `TicketResource` + `CreateTicket` page
- owner picker runtime: modulo Geo (`CoordinatePicker` family)
- fix CSS frontoffice nel tema Sixteen non sono automaticamente garanzia per admin panel

## Root-cause candidate (checklist)

1. container visibility timing nello step wizard
2. `invalidateSize`/redraw non eseguiti nel momento giusto
3. CSS Leaflet non allineato al contesto render attuale
4. differenza asset pipeline frontoffice vs panel

## Regola operativa

Un fix mappa su admin panel e' completo solo dopo verifica diretta della route admin, non per analogia con il frontoffice.

## Riferimenti

- [geo admin panel map visibility contract](../../../Geo/docs/wiki/concepts/filament-admin-panel-map-visibility-contract.md)
- [theme not owner for admin panel styles](../../../../Themes/Sixteen/docs/wiki/concepts/filament-admin-style-ownership-boundary.md)
