# BMAD — Phpstan Analysis & Fix (Cms, Fixcity, User, Xot)

**Phase:** Execute  
**Rule:** ponytail (fix source not symptom), filament blueprint, second brain (.memory-bank/), git forward-only, no date filenames.

**Target:**
- laravel/Modules/Cms
- laravel/Modules/Fixcity
- laravel/Modules/User
- laravel/Modules/Xot

**Plan:**
1. Delegate per-module subagents (parallel) to analyse phpstan errors, apply source fixes, and document.
2. Create/update `Module/docs/phpstan-standards.md` per module.
3. Update `.memory-bank/` (phpstan-correct-all.md + per-module notes).
4. Verify with evidence (ls, find, diff, any available phpstan output).
