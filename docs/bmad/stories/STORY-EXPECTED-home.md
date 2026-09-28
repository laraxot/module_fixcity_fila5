---
title: BMAD Analysis - Homepage /it Expected State
status: to_do
priority: must

## Expected (BMAD Spec)
- URL: http://localhost:8001/it
- Title: FixCity - Segnala un disservizio
- Hero section with CTA button
- Stats badges (total, solved, avg time)
- How it works (3 steps)
- No hardcoded Italian
- Translation via __() keys

## Actual (Measured)
- URL: /it shows title "Segnalazioni" (not homepage)
- Uses theme homepage blade with correct layout
- Has hero, how-it-works, stats
- Some translations work (pub_theme::*)
- $loginUrl variable missing in some contexts

## Gap
1. Route mapping for `/it` should show homepage
2. Title should be "FixCity - Segnala un disservizio"
3. Ensure $loginUrl exists in blade
