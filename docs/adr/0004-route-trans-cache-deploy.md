---
qmd: "adr deploy cache rotte route trans cache laravel localization"
title: "Il deploy tiene la cache delle rotte per lingua"
type: decision
status: accepted
created: 2026-11-17
updated: 2026-11-26
module: Fixcity
decision-makers: [team Laraxot]
tags: [adr, deploy, prestazioni, rotte, laravel-localization]
issues:
  - "https://github.com/laraxot/module_fixcity_fila5/issues/74"
related:
  - ../../../../../deploy.sh
---

# ADR: il deploy tiene la cache delle rotte per lingua

## Contesto

Un tester ha segnalato il sito lento (issue #74). `deploy.sh` eseguiva `php artisan optimize` e subito dopo `php artisan route:clear`: la cache delle rotte appena costruita veniva buttata, e ogni
richiesta registrava tutte le rotte da capo. Il sito usa mcamara/laravel-localization (it, en, de, es): un `route:cache` semplice non copre le rotte tradotte. Come si tiene una cache delle rotte
senza rompere le rotte nelle lingue diverse da quella predefinita?

## Driver della decisione

- Tempo di risposta delle pagine pubbliche (misurato: `/it/tickets`).
- Nessuna regressione nelle rotte tradotte: stessi codici HTTP in tutte e quattro le lingue.
- Un deploy che fallisce a meta' lascia il sito in manutenzione: il ripiego deve riportare online l'applicazione.

## Alternative scartate

- Lasciare `route:clear`: nessun rischio, ma la cache delle rotte non c'e' mai.
- `route:cache` semplice: rompe le rotte nelle lingue diverse da quella predefinita.

## Decisione

Dopo `optimize`, `php artisan route:trans:cache` (una cache per lingua); se fallisce, `route:clear`, cioe' il comportamento di prima, invece di lasciare il sito in manutenzione. Nel codice:
`deploy.sh` (radice del progetto), blocco "Cache delle rotte per lingua", riga `php artisan route:trans:cache --quiet || php artisan route:clear --quiet`.

## Conseguenze

- Buono: circa 10% in meno sul tempo di `/it/tickets` misurato in locale; 60 pagine in 4 lingue con gli stessi codici HTTP.
- Cattivo: una rotta registrata fuori dal ciclo normale (closure, rotte aggiunte a runtime) non entra nella cache; il ripiego su `route:clear` copre solo il fallimento del comando, non una rotta
  mancante.
- Da misurare in produzione: TTFB prima e dopo; il resto del piano di #74 (cache store, response cache, OPcache) resta aperto.

## Conferma

Nessuna conferma automatica oggi: nessun test Pest o Arch legge `deploy.sh`. Controllo manuale dalla radice del progetto (`laravel/..`):

```bash
rg -n "route:trans:cache|route:clear|route:cache" deploy.sh
```

Oggi mostra tre righe di commento (216-218) e la riga 221 con `route:trans:cache` seguito dal ripiego `route:clear`; non deve comparire un `php artisan route:clear` o `route:cache` isolato dopo `optimize`. Dopo un deploy,
da `laravel/`: `php artisan route:list` non deve dare errori e `/it/tickets`, `/en/tickets`, `/de/tickets`, `/es/tickets` devono rispondere con lo stesso codice HTTP.
Proposta: un test Arch o di file che verifichi la riga `route:trans:cache` in `deploy.sh`.
