---
title: "SET BODY TAG SIMPLE"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-26
qmd: "SET BODY TAG SIMPLE"
issues: []
discussions: []
---

Motivazione

Il tema Sixteen ha ora il tag <body> semplificato per allinearsi a design-comuni. Questo evita conflitti di stile quando si usa la parity CSS e il sistema Tailwind + Alpine.

Cosa fare nei moduli

- Non affidarsi a classi presenti sul tag <body> nel tema. Applicare classi su contenitori locali quando necessario.
- Se un modulo richiede una classe globale, documentare la necessità e proporre una soluzione centralizzata nel tema.

Questa nota è stata aggiunta automaticamente dal processo di auditing.