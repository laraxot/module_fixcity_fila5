---
name: segnalazione-map-runtime-boundary-rule
description: Responsibility split for the segnalazione wizard map between Fixcity, Geo, and Sixteen
---

# Segnalazione Map Runtime Boundary Rule

## Rule

Nel wizard `segnalazione-crea` la mappa segue una boundary netta:

- **Fixcity** definisce flusso, step, campi e semantica del form;
- **Geo** definisce il comportamento runtime della mappa;
- **Sixteen** definisce la resa visuale coerente con Design Comuni.

## Practical Consequences

- Fixcity non deve introdurre loop di refresh, `setTimeout` locali o CSS dedicato al ticket wizard per correggere Leaflet.
- Se la mappa lampeggia, il fix va nel runtime Geo.
- Se il box mappa o i controlli non hanno parity visuale, il fix va nel tema Sixteen.
- Il widget Fixcity deve limitarsi a configurare il field e consumarne gli eventi/stati.

## False Friend

"Il bug appare nel wizard, quindi la soluzione appartiene al wizard."

Falso: se il difetto e' refresh, tile loading, fullscreen runtime o marker sync, l'owner e' quasi sempre il modulo Geo.
