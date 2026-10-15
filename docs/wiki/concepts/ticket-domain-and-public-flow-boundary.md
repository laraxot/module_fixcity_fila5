---
title: "Ticket Domain and Public Flow Boundary"
type: concept
module: Fixcity
sources:
  - "../../../README.md"
  - "../../../00-INDEX.md"
confidence: high
created: 2026-10-14
updated: 2026-10-14
tags: [fixcity, ticket, folio, cms, geo, public-flow]
related:
  - "../../../../Cms/docs/wiki/concepts/cms-content-routing-theme-boundary.md"
  - "../../../../Geo/docs/wiki/concepts/geo-data-and-map-boundary.md"
  - "../../../../../Themes/Sixteen/docs/wiki/concepts/legal-consent-ui-boundary.md"
---

# Ticket domain and public flow boundary

`Modules/Fixcity` owns citizen reports, ticket status and the service-specific
workflow. It does not own the generic CMS resolver, geographic primitives or the
Sixteen visual system.

## Ownership map

- Ticket entity, enums, actions and wizard schema: Fixcity.
- Page JSON and Folio resolution: Cms.
- Coordinates, geocoding and map datasets: Geo.
- Public markup, responsive styling and Design Comuni parity: Sixteen.
- Authenticated identity and account lifecycle: User; privacy evidence: Gdpr.

The public ticket list and map must consume the same filtered dataset. GeoJSON uses
`[longitude, latitude]`; a map rendering change must not silently alter ticket
visibility or authorization.

## Verification order

1. Confirm the ticket action/query and tenant data.
2. Confirm the public page resolver and localized JSON.
3. Confirm the GeoJSON contract and map payload.
4. Only then inspect Sixteen markup/CSS and visual evidence.

## References

- [CMS content/routing boundary](../../../../Cms/docs/wiki/concepts/cms-content-routing-theme-boundary.md)
- [Geo data/map boundary](../../../../Geo/docs/wiki/concepts/geo-data-and-map-boundary.md)
- [Documentation ownership](../../../../../../bmad-output/docs-architecture/ownership.md)

