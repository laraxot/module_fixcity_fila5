---
name: segnalazione-crea-geolocate-when-empty
description: CoordinatePicker in segnalazione-crea must geolocate when coordinates are missing
type: concept
---

# Segnalazione Crea Geolocate When Empty

For `/it/tests/segnalazione-crea?step=form.dati-della-segnalazione::data::wizard-step`, when no coordinates are provided, the map must center to the current user position.

Implementation contract:

- `CreateTicketWizardWidget` must configure `CoordinatePicker::make('location')->geolocateWhenEmpty()`.
- `coordinate-picker-lit` handles browser geolocation and updates state.
- If browser geolocation is denied, fallback center is used without blocking the step.
