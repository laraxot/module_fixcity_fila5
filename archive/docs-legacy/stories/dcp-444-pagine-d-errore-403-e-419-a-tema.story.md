---
title: "DCP-444: pagine d'errore 403 e 419 con il layout del tema"
type: story
status: done
module: Fixcity
created: 2026-11-12
updated: 2026-11-12
tags: [bmad, story, front-office, errori, 403, 419, i18n, a11y]
qmd: "403 419 pagina d'errore a tema sessione scaduta accesso non consentito HttpException bootstrap app errors access"
related:
  - ./dcp-151-pagina-404-del-front-office-con-il-layout-del-tema.story.md
  - ../decisions/themed-access-errors-through-http-exception-handler.md
  - ../../../../Themes/Sixteen/docs/stories/front-office-production-readiness-audit.story.md
issues: []
discussions: ["https://github.com/laraxot/theme_sixteen_fila5/discussions/94"]
---

# DCP-444: pagine d'errore 403 e 419 con il layout del tema

Status: **done** (chiusa il 2026-11-11 nell'audit L-12; documentata qui il 2026-11-12). Dettaglio dell'audit: riga L-12 della story
`front-office-production-readiness-audit.story.md`.

## Richiesta

Utente, 2026-11-11: controllare integralmente il front office, da loggato e da non loggato, per andare in produzione; correggere ciò che non si
vede come dovrebbe.

## Misurato (prima)

Un POST senza token CSRF e un accesso negato davano la pagina **nuda di Laravel in inglese** («Page Expired», «Forbidden»), senza header, footer e
senza la lingua del cittadino. DCP-151 aveva coperto solo il 404 e dichiarava 403 e 419 «non coperti».

## Soluzione

| Dove | Cosa |
|---|---|
| `Modules/Fixcity/resources/views/errors/access.blade.php` | una vista per accesso negato e sessione scaduta (stessa struttura di `not-found.blade.php`): titolo, messaggio, pulsante «Accedi» e pulsante «Vai alla home» |
| `Modules/Fixcity/lang/{it,en,de,es}/error_pages.php` | chiavi `expired`, `forbidden` (titolo e messaggio), `login`, `home` |
| `laravel/bootstrap/app.php` | gestore `HttpException` per 403 e 419 con **lo stesso filtro del 404**, estratto in due closure (`$isCitizenHtmlRequest`, `$useRequestLocale`): nessun elenco di percorsi duplicato |

Non cambia: API, Livewire, risposte JSON, back office, asset e storage restano sulle pagine standard. Il messaggio dell'eccezione non viene mai
mostrato al cittadino (il test usa un messaggio sentinella e verifica che non compaia). Se la vista fallisce, il gestore ritorna `null` e vale la pagina
standard: un errore nella pagina d'errore non nasconde l'errore originale.

## Criteri di accettazione

- [x] 403 a tema in it, en, de, es, con lingua della richiesta.
- [x] 419 a tema in it, en (e de verificato sul dev con un 419 vero).
- [x] I pulsanti non hanno `href="#"` e portano a login e home della lingua.
- [x] API e risposte JSON restano standard.
- [x] Guardia che diventa rossa se la mappa degli stati viene svuotata.

## Verifica

| Prova | Esito |
|---|---|
| `Modules/Fixcity/tests/Feature/Pages/ThemedAccessErrorPagesTest.php` (8 test) | verdi; con la mappa 403/419 svuotata **7 rossi** (resta verde solo il caso API/JSON, giusto così) |
| `ThemedNotFoundPageTest` | 7/7 invariato |
| 419 vero sul dev: `POST /it/auth/logout` e `/de/auth/logout` senza token | pagina a tema con titolo, header e footer; a 375 e 1440 px senza overflow; pulsanti a 48 px |
| PHPStan e Pint sui file toccati | 0 errori, passed |

## Non coperto

- 500, 503 e il 429 di login e registrazione restano la pagina standard (i 500 vanno tenuti minimali: il layout potrebbe essere la causa).
- axe-core sulla pagina 419 non è stato eseguito: confluisce in DCP-452.
- Il 419 su una richiesta Livewire non passa da qui di proposito (il frontend di Livewire gestisce la scadenza della sessione).

## Lezione

Per la prova inversa su un file condiviso (`bootstrap/app.php`) ho lavorato su una copia di backup rimessa con `cp` subito dopo, con `php -l` e conteggio
delle righe. Il rischio resta con un altro agente che scrive lo stesso file nello stesso minuto: dove si può, la prova inversa va fatta su una copia del
test.
