# Naming Convention: ticket vs segnalazioni

## Rule
In code and technical artifacts, **always use `ticket`** instead of `segnalazioni` because `segnalazioni` is the Italian translation of `ticket`.

## Rationale
- `segnalazioni` = Italian word for "tickets/reports" 
- `ticket` = canonical English term used throughout the codebase
- Consistency prevents confusion and redundancy

## What to Use Where

### Code, Components, Classes, Methods → `ticket`
```
✓ components/blocks/ticket/
✓ Ticket.php model
✓ ticket-list.blade.php
✓ ticket-tabs-bar (instead of segnalazioni-tabs-bar)
```

### Translations, Labels, UI Text → `segnalazioni` (Italian) or `tickets` (English)
```
✓ fixcity::segnalazione (Italian UI)
✓ fixcity::ticket (English UI)
✓ "Elenco segnalazioni" (page title - Italian)
```

### HTML/CSS IDs and wrapper classes → Match Design Comuni reference EXACTLY
```
✓ id="segnalazioni-elenco-root" (Design Comuni reference)
✓ class="segnalazioni-elenco" (in class attribute)
✗ class="ticket-elenco" (breaks Playwright #segnalazioni-elenco-root selector)
```

**IMPORTANT**: Playwright and CSS theme tests expect `#segnalazioni-elenco-root`.

## Affected Files to Refactor
- `laravel/Themes/Sixteen/resources/views/components/blocks/tabs/map-list.blade.php` → rename class `segnalazioni-layout` to `ticket-layout`
- `laravel/Themes/Sixteen/resources/css/` → all `.segnalazioni-elenco-*` classes
- `laravel/Modules/Geo/tests/Playwright/` → test file names
- `public_html/data/tickets.json` → canonical data source

## Enforcement
- Check all component names, CSS classes, JS variables
- Search before creating new files: `grep -r "segnalazioni" --include="*.php" --include="*.blade.php"`