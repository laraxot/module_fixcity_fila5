---
title: "Site-wide front-office search for citizens"
type: story
status: review
module: Fixcity
created: 2026-10-26
updated: 2026-10-26
tags: [bmad, story, fixcity, front-office, search, design-comuni, a11y, i18n]
qmd: "site wide search ricerca SearchFrontOfficeAction search-results design-comuni search modal q tickets blog events cms pages"
related:
  - ../../../../Themes/Sixteen/docs/stories/front-office-completeness-audit.story.md
  - ../../../Blog/docs/stories/blog-events-listing-body-and-search.story.md
issues: []
discussions: []
---

# Site-wide front-office search for citizens

Status: **review**. BMAD phase: Quick Flow (story, then dev story, then code).

## Why

The front-office audit (Sixteen `front-office-completeness-audit`, open item 9) found that the header search covered only tickets.
Another session had added `Themes/Sixteen/resources/views/pages/ricerca.blade.php`: a stub that always showed 0 results and broke the
theme page rule (`NoSemanticFolioPageDirectoriesTest`).

## What was built

| Part | File |
|---|---|
| Action (owner: Fixcity, the site module that already depends on Blog and Cms; Cms or Blog would have to depend on tickets) | `app/Actions/Search/SearchFrontOfficeAction.php` |
| Typed results | `app/Datas/FrontOfficeSearchItem.php`, `app/Datas/FrontOfficeSearchResult.php` |
| Shared query cleaning (LIKE wildcards stripped, 100 chars) | `PublicTicketListFilters::cleanQuery()` is now public |
| Posts and events query | Blog `ThemeArticleQueries::searchPublishedPostsAndEvents()`; same "published" rules as the detail pages; the LIKE clause is shared with `paginatedPosts()` |
| Results page | CMS `config/local/fixcity/database/content/pages/ricerca.index.json` (it, en, de, es), served by `[container0]` as `/{lang}/ricerca` |
| Block | `search-results/design-comuni` with `source: site_search` includes `search-results/live.blade.php`; without it, the static reference page (`tests.risultati-ricerca`) is unchanged |
| Texts | `lang/{it,en,de,es}/site_search.php` |
| Header modal | `sections/search-modal/design-comuni` sends `q` to `FrontofficeRoutes::searchIndex()`; labels in `sections/search-modal.json` now say "the site" |

Sources and caps (10 per source, `LIMIT_PER_SOURCE`):
- public tickets: `ListPublicTicketsAction` with its `q` filter (public statuses only, no author data). When more tickets match, the page links the full tickets list with the same `q`;
- published posts and events (title, description, excerpt);
- public CMS pages `<container>.index` (no middleware, not `ricerca` itself) whose title in the current locale contains the query. Read only.

Empty query: no search runs and the page shows a prompt. Accessibility: visible H1, breadcrumb, a GET `role="search"` form with a label,
the count in `role="status"`, results as a `<ul>` of links.

The page JSON is named `ricerca.index.json` (not `ricerca.json`) because `[container0]` looks up the slug `<container>.index`, like
`mappa-sito.index.json`.

## Verification

- `Themes/Sixteen/tests/Feature/Pages/SiteSearchPageTest.php` (7 tests) and `SearchModalFormTest.php` (8 tests): 15 passed.
- `NoSemanticFolioPageDirectoriesTest`, `SitemapPageTest`, `DetailNotFoundTest`, `ListPublicTicketsActionTest`: passed.
- `Modules/Blog/tests/Feature/ThemeArticleListingTest.php` alone: 5 passed.
- PHPStan on the touched PHP files: 0 errors. Pint: clean.

## Open

1. The dev DB has no `articles.content_type`, so `/ricerca` with a query returns 500 on dev until the Blog migration runs (same issue as blog and events, audit item 1).
2. `DetailNotFoundTest` creates an article on the `blog` connection, which the root `Tests\TestCase` does not roll back. Run in the same process before `ThemeArticleListingTest`, it makes that test fail (16 posts instead of 15). This was already the case before this story.
3. CMS pages match by title only. Body text and FAQ answers are not searched yet.
