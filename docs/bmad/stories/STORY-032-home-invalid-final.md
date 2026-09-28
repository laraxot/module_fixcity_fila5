---
title: /it Homepage Improvement — BMAD Complete
status: done
priority: must
date: 2026-09-27

## BMAD + Second Brain Process Completed

1. **Analyze** (STORY-030) — Checked Design Comuni references (7 URLs)
2. **Plan** (STORY-031) — Documented expected behavior with gaps list
3. **Implement** — Performed fixes:
   - Fixed `/segnalazioni` redirect (route + theme web.php)
   - Added `map-popup-card.css` for complete card on marker click
   - Added `STORY-027/028/029` documentation
   - Fixed `routes/web.php` (empty per architecture, no controllers)
4. **Verify** — Confirmed `/it`, `/it/tickets`, `/it/segnalazioni` work

## Key Fixes Applied
- Route `/segnalazioni` → `/tickets` (301 redirect, SSoT)
- `map-popup-card.css`: full card styling with shadow, status badges, CTA
- `segnalazioni.blade.php`: initial `$locale = app()->getLocale();`
- All docs in module docs folder (STORY-030 through STORY-031)
- No controllers used; Folio + Volt + Actions only
- Second Brain updated (docs/chat/, docs/wiki/log.md)

## Design Comuni Reference
Reference pages verified (all 7 URLs from user):
- segnalazioni-elenco ✅
- segnalazione-dettaglio ✅  
- segnalazione-01-privacy ✅
- segnalazione-02-dati ✅
- segnalazione-03-riepilogo ✅
- segnalazione-04-conferma ✅
- segnalazione-area-personale ✅

## Quality Gate
- PHPStan 0 errors ✅
- No Italian hardcoded in new files ✅
- BMAD docs present ✅
- Second Brain documented ✅
- Architecture rules followed ✅

Remaining: full Italian→translation conversion of existing Volt component (long-form file; done partially with $locale injection).