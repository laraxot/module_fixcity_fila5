---
title: "FixCity translation keys"
type: note
status: draft
updated: 2026-10-19
tags: [docs, fixcity]
qmd: "fixcity translation keys fixcity"
---
# FixCity translation keys

FixCity follows the shared five-element translation contract:

```text
<namespace>::<context>.<collection>.<key>.<type>
```

The namespace counts as the first element. Auth examples are therefore:

- `fixcity::auth.login.intro.text`
- `fixcity::auth.login.password.placeholder`
- `fixcity::auth.register.title.text`
- `fixcity::auth.register.meta_description.text`

Incomplete forms such as `fixcity::auth.intro` and `fixcity::auth.password`
are legacy keys. When replacing one, update the Blade caller and every
supported locale in the same change; do not silently add a second localization
engine or leave a fallback-only key in a public page.

This document is the FixCity-module reference for the BMAD/Second Brain
translation contract. The theme-specific accessibility key is documented in
`Themes/Sixteen/docs/bmad/translation-keys-5-elements.md`.
