---
title: "FixCity continuous quality memory"
type: memory
tags: [fixcity, quality, migrations, owner, design-comuni]
created: 2026-10-09
updated: 2026-10-09
qmd: "FixCity owner ticket authenticated creation migration single owner Design Comuni contrast"
status: draft
---

# Durable decisions

- La creazione di un ticket richiede un utente autenticato: `owner_id` viene
  risolto nel widget/action e non si inseriscono ticket anonimi con `NULL`.
- `ticket_comments.user_id` resta compatibile con l'identità effettivamente
  adottata dal modulo; non si converte un `bigint` legacy in UUID con una
  migrazione runtime. Lo schema canonico va corretto nella migrazione owner.
- Le migrazioni `align_*` e `fix_*` che tentano correzioni speculative sono
  rumore architetturale: la soluzione è rimuoverle e mantenere una migrazione
  owner coerente, con timestamp bump secondo la regola del modulo.
- `public_html/themes/Sixteen` è il symlink verso `laravel/Themes/Sixteen/public`;
  il browser deve consumare gli asset dal build del tema, senza copie divergenti.
