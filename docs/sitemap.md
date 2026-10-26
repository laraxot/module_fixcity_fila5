---
title: "Sitemap — Fixcity"
type: documentation
status: active
tags: [fixcity, sitemap, dcp-273, dcp-148]
created: 2026-10-26
updated: 2026-10-26
qmd: "sitemap   fixcity fixcity"
---

# Sitemap — Fixcity

## Contenuto
La sitemap include:
- `/{lang}/tickets/{id}` per ogni segnalazione **pubblica** (`BuildPublicTicketsQueryAction` con `includeOwnTickets: false`, `status` in `canViewByAll()`, `location` non null)

## Fonti
`Fixcity\Actions\Ticket\ListPublicTicketPathsAction` implementa `SitemapPathsContract`, registrato in `FixcityServiceProvider::register()`.

## Vedi
- `Modules/Fixcity/docs/wiki/concepts/sitemap-ticket-folio-routing.md`
- `Modules/Seo/docs/stories/dcp-274-sitemap-completeness-verification.story.md`
