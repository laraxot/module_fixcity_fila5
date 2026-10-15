---
title: Wizard map button transparent — invisible ctrl-btn on map
description: Buttons inside the wizard map have bg-transparent making them invisible; ctrl-btn needs visible Design Comuni styling.
status: open
priority: must
github_issue: "#14"
discussion: "Wizard map wizard buttons invisible — ctrl-btn has no background"
module: Geo / Fixcity
qmd: wizard-map-buttons
links: {github_issue: "#14", discussion: "design-comuni-button-spec"}
type: story
tags: [design-comuni, accessibility, wizard, map, button]
updated: 2026-10-14
---

## Bug
The wizard map wizard buttons (`.ctrl-btn` in `geolocation.js`) are rendered with `bg-transparent`, making them invisible against the map background. Users cannot locate or click the geolocation control.

## Fix applied
- Added inline design-system styling using Design Comuni accent `#007a52`.
- Added `:hover` (`#005c3d`) and `:focus-visible` (outline + darker background) states.
- Added `border-radius:6px`, `box-shadow`, `font-weight:600`, and `transition` for visibility.
- File: `laravel/Modules/Geo/resources/js/components/map/controls/geolocation.js` (`renderButton`).

## Design Comuni reference
Button spec: accent `#007a52`, hover `#005c3d`, focus-visible outline `2px solid #005c3d` offset `2px`. No Bootstrap; native CSS tokens only.
