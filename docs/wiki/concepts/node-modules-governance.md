---
title: "Node Modules Governance — Project Root Only"
type: concept
sources:
  - "https://docs.npmjs.com/cli/v10/configuring-npm/package-json"
confidence: high
created: 2026-05-04
updated: 2026-05-04
tags: [node_modules, npm, governance, project-structure, dependencies]
related:
  - ../../../laravel/Themes/Sixteen/docs/wiki/concepts/theme-owned-css-parity-rule.md
  - ../../../laravel/Modules/Fixcity/docs/wiki/concepts/segnalazione-design-comuni-comparison.md
---

# Node Modules Governance — Project Root Only

> **Rule**: `node_modules/` MUST NOT exist in subdirectories like `bashscripts/ai/.agents/`.
> **Correct location**: `./node_modules/` at project root (`/var/www/_bases/base_fixcity_fila5/node_modules/`).

## Why `bashscripts/ai/.agents/node_modules/` is WRONG

### The Problem

1. **`package.json` in wrong location**: `bashscripts/ai/.agents/package.json` caused `npm install` to create `node_modules/` in `.agents/` subdirectory
2. **Wrong relative paths**: `package-lock.json` contained paths like `../bashscripts/ai/.agents/node_modules/...`
3. **Broken references**: Any script referencing `.agents/node_modules/` will fail or use wrong dependencies

### The Fix

**Correct structure:**
```
/var/www/_bases/base_fixcity_fila5/
├── node_modules/              # ✅ Correct: project root
├── package.json             # ✅ Root dependencies
├── bashscripts/
│   └── ai/
│       └── .agents/
│           ├── package.json    # ❌ Remove or move deps to root
│           └── package-lock.json  # ❌ Contains wrong paths
```

## Correct Approach

### If `.agents/` needs dependencies:

1. **Move to root `package.json`**:
   ```bash
   # Merge .agents/package.json dependencies into root package.json
   # Then remove .agents/package.json
   ```

2. **Reinstall at root**:
   ```bash
   cd /var/www/_bases/base_fixcity_fila5
   npm install
   ```

3. **Remove wrong directories**:
   ```bash
   rm -rf bashscripts/ai/.agents/node_modules/
   rm bashscripts/ai/.agents/package-lock.json  # Regenerate at root
   ```

## Current State (2026-05-04)

| Path | Status | Action |
|------|--------|--------|
| `bashscripts/ai/.agents/node_modules/` | ✅ Removed | Was wrong location |
| `bashscripts/ai/.agents/package.json` | ⚠️ Needs merge | Move deps to root |
| `bashscripts/ai/.agents/package-lock.json` | ⚠️ Wrong paths | Regenerate at root |
| `./node_modules/` (root) | ❌ Missing | Run `npm install` at root |

## Dependencies in `.agents/package.json`

```json
{
  "dependencies": {
    "@kilocode/plugin": "7.2.25",
    "@opencode-ai/plugin": "1.14.28",
    "playwright": "^1.59.1",
    "puppeteer": "^24.40.0"
  }
}
```

**These should be merged into root `package.json`** to ensure:
- Single `node_modules/` at project root
- Correct import paths in all scripts
- Proper dependency resolution

## Verification Script

```bash
# Check no nested node_modules exist
find /var/www/_bases/base_fixcity_fila5 -name "node_modules" -type d | grep -v "^/var/www/_bases/base_fixcity_fila5/node_modules"

# Should return empty (no output)
```

## Module/Theme Docs Update

### Sixteen Theme
- ✅ `laravel/Themes/Sixteen/docs/wiki/concepts/theme-owned-css-parity-rule.md` (exists)

### Fixcity Module  
- ✅ `laravel/Modules/Fixcity/docs/wiki/concepts/segnalazione-design-comuni-comparison.md` (exists)

### Root Docs
- ✅ `docs/wiki/concepts/node-modules-governance.md` (this file)

## QMD Ingest

```bash
# After creating this document:
qmd collection add docs/wiki/ --name root
qmd update --name root
```

## Story Update

**Story 8-74** (agents-dir-audit): `done` ✅  
**Story 7-104** (segnalazione-01-privacy): `done` (P0 ✅, P1-P4 pending)

---

**Last updated**: 2026-05-04 by LLM Wiki Maintainer  
**Files modified**: 
- `docs/wiki/concepts/node-modules-governance.md` (new)
- `bashscripts/ai/.agents/node_modules/` (removed ✅)
**QMD ingest**: Pending (`qmd update --name root`)
