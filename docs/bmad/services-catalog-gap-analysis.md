---
title: "Fixcity service catalogue — reference comparison and gaps"
type: bmad-gap-analysis
status: audited
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, design-comuni, services, gap-analysis]
qmd: "Fixcity services catalogue comparison Design Comuni gaps routes data fake contact"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../../../Themes/Sixteen/docs/bmad/services-catalog-expected-2026-09-27.md
  - ../../../../Themes/Sixteen/docs/wiki/concepts/comuni-design-system-guidance.md
  - correction-plan-services-catalog-2026-09-27.md
---

# Service catalogue: observed gaps

## Reference → current project

| Design Comuni capability | Current evidence | Gap / decision |
|---|---|---|
| Site-wide civic information architecture | Sixteen has a Folio services page and Fixcity ticket journeys | The broader directory should not imply every municipal service is implemented |
| Search and result feedback | The page search filters the three visible report/list/track tasks and announces count/empty state | Search scope is explicit and localized |
| Category catalogue | The catalogue exposes one verified “public reports” group linked to the real ticket list; it does not claim unrelated registry, tax, planning, social, environment or culture services | Keep the category list scoped to implemented, tenant-verified services |
| Featured service actions | The service entry presents implemented report, list and tracking journeys; the report detail links to the authenticated creation flow | All visible task links are real localized FixCity routes; sign-in is disclosed before starting |
| Verified municipality information | The public catalogue and footer do not invent URP phone/email details | Add tenant contacts only after the municipality provides and approves them |
| Access prerequisite | Guest report route redirects to localized login | Tell users on the service entry card that sign-in is required before starting a report |
| Service detail | `/services/report-issue` documents the real report service and links to the create flow | Covered for FixCity reporting; other municipal service details need verified content and integrations |
| Report service-sheet actions | Design Comuni provides both a report action and an all-reports action; the local detail previously linked only to report creation | Add a localized secondary action to the verified public list at `/tickets` |
| Contacts and service conditions | The reference includes a contact office and terms link; FixCity has no approved tenant contact or published service terms in this page | Keep those sections absent until the tenant supplies and approves authoritative content |
| Locale parity | IT/EN/DE/ES dictionaries cover current tasks and search feedback | Keep the supported public-reports group localized across all four locales |
| Accessible feedback | Search has a label, `aria-controls`, polite result count and empty state | Preserve; category actions must have descriptive, working destinations |

## Product scope

The project’s verified operational service is reporting and following public
issues (`/tickets`, `/tickets/create`, `/tickets/track`, and personal practices).
No current evidence verifies PagoPA, SUAP, registry certificates, social
services, appointment booking, tenant URP phone or email. These are content and
integration requirements, not UI copy to fabricate.

## Wider Design Comuni information architecture

The official index also includes general discovery pages (FAQ, search results,
topics, resource/category listings and sitemap), Administration, News, civic
events, appointment booking and assistance requests. Fixcity is a focused
reporting service rather than a complete municipal CMS: those sections need
separate owner modules/content sources and are not created by this catalogue
change. The reference's disservice flow is directly relevant and maps to the
existing report wizard, public list/map, tracking and citizen practices. The
service-detail template exists at `/services/report-issue`; the remaining
detail-page gap is the secondary action to the public list. Do not copy sample
contacts, terms, appointment options, or related services without tenant-owned
content.

## Verification baseline

The active Folio route is `/it|en|de|es/services`, rendered by
`Themes/Sixteen/resources/views/pages/services/index.blade.php`. Existing page
content and service translations are static and do not constitute a service
catalogue data model. The official service-workflow index
`https://italia.github.io/design-comuni-pagine-statiche/servizi/index.html`
is separate from the root site index. It covers shared workflow steps plus
ranking applications, permits/authorizations, economic benefits, pagoPA fines,
IMU/F24 and paid services. The root index (v2.4.0) separately describes site
pages and the disservice-report journey: service sheet, privacy, data, review,
confirmation, personal area and public list. A direct crawl found 35 unique
`/sito/` templates and 44 unique workflow pages; every linked page returned
HTTP 200. Eight appointment templates cover both guest and authenticated
applicants. The two index documents are excluded from these counts. An earlier
workflow count of 45 was an off-by-one and remains corrected to 44. These are reference templates,
not proof that FixCity implements every municipal transaction. Keep the current
catalogue honest; create owner stories and integrations before adding other
service families.

The report-detail template also includes feedback, terms, verified contacts,
and related municipal content. Those elements remain explicit gaps until the
tenant supplies trustworthy sources; adding static demo values would reduce
service accuracy. The implemented improvement adds the missing route to all
public reports beside the report-submission action.

### Current browser evidence

On 2026-09-27, the locally running `/it/privacy` returned HTTP 200 and rendered
tenant-localized demo Markdown. This confirms the route/template, not legal
approval or readiness for public deployment. The page copy and checkbox
semantics still require tenant/privacy-owner review; see G-30/G-31 and the
release plan. Do not describe demo policy content as approved.
