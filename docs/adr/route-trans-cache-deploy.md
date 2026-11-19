---
qmd: "adr deploy cache rotte route trans cache laravel localization"
title: "ADR — Il deploy tiene la cache delle rotte per lingua"
type: decision
module: Fixcity
tags: [adr, deploy, prestazioni, rotte, laravel-localization]
created: 2026-11-17
updated: 2026-11-18
status: accepted
issues:
  - "https://github.com/laraxot/module_fixcity_fila5/issues/74"
related:
  - ../../../../../deploy.sh
---

# ADR: il deploy tiene la cache delle rotte per lingua

## Contesto

- Un tester ha segnalato il sito lento (issue #74).
- `deploy.sh` eseguiva `php artisan optimize` e subito dopo `php artisan route:clear`: la cache delle rotte appena costruita
  veniva buttata, e ogni richiesta registrava tutte le rotte da capo.
- Il sito usa mcamara/laravel-localization (it, en, de, es): un `route:cache` semplice non copre le rotte tradotte.

## Decisione

- Dopo `optimize`, `php artisan route:trans:cache` (una cache per lingua); se fallisce, `route:clear`, cioe' il comportamento
  di prima, invece di lasciare il sito in manutenzione.

## Alternative scartate

- Lasciare `route:clear`: nessun rischio, ma la cache delle rotte non c'e' mai.
- `route:cache` semplice: rompe le rotte nelle lingue diverse da quella predefinita.

## Conseguenze

- Pro: circa 10% in meno sul tempo di `/it/tickets` misurato in locale; 60 pagine in 4 lingue con gli stessi codici HTTP.
- Contro: una rotta registrata fuori dal ciclo normale (closure, rotte aggiunte a runtime) non entra nella cache; il ripiego
  su `route:clear` copre solo il fallimento del comando, non una rotta mancante.
- Da misurare in produzione: TTFB prima e dopo; il resto del piano di #74 (cache store, response cache, OPcache) resta aperto.
