---
title: "403 e 419 a tema: un solo gestore HttpException con il filtro del 404"
type: note
status: accepted
module: Fixcity
created: 2026-11-12
updated: 2026-11-12
tags: [docs, fixcity, decisions, errori, 403, 419, i18n]
qmd: "403 419 pagina d'errore a tema gestore HttpException bootstrap app filtro 404 lingua della richiesta"
related:
  - ../stories/dcp-444-pagine-d-errore-403-e-419-a-tema.story.md
  - ../stories/dcp-151-pagina-404-del-front-office-con-il-layout-del-tema.story.md
  - ../../../../Themes/Sixteen/docs/stories/front-office-production-readiness-audit.story.md
issues: []
discussions: ["https://github.com/laraxot/theme_sixteen_fila5/discussions/94"]
---

# 403 e 419 a tema: un solo gestore `HttpException` con il filtro del 404

Decisione presa il 2026-11-11 nell'audit del front office (riga L-12), registrata il 2026-11-12. Story: [DCP-444](../stories/dcp-444-pagine-d-errore-403-e-419-a-tema.story.md). Confronto con le altre decisioni dell'audit: discussion [#94](https://github.com/laraxot/theme_sixteen_fila5/discussions/94) (riga R-4).

## Contesto

Un `POST` senza token CSRF (419) e un accesso negato (403) davano la pagina nuda di Laravel, in inglese, senza header né footer e senza la lingua del cittadino. DCP-151 aveva portato a tema solo il 404 e dichiarava 403 e 419 «non coperti». Il 404 usa già un gestore in `laravel/bootstrap/app.php` con due closure: `$isCitizenHtmlRequest` (decide se la richiesta è di un cittadino e vuole HTML) e `$useRequestLocale` (imposta la lingua della richiesta).

## Decisione

Un **solo gestore** `HttpException` in `laravel/bootstrap/app.php` per 403 e 419, che **riusa le stesse due closure del 404**:

- mappa degli stati: `[403 => 'forbidden', 419 => 'expired']`; qualunque altro stato esce subito (`return;`) e vale la pagina standard;
- il filtro `$isCitizenHtmlRequest` esclude `api/*`, `livewire/*`, `themes/*`, `storage/*`, `data/*`, `*/assets/*`, `fixcity/*` e `*/admin/*`: API, Livewire, back office e asset restano sulle pagine standard;
- vista unica `Modules/Fixcity/resources/views/errors/access.blade.php` (`fixcity::errors.access`), stessa struttura di `not-found.blade.php`, con pulsanti «Accedi» e «Vai alla home» verso login e home **della lingua della richiesta**;
- testi nelle chiavi `expired`, `forbidden` (titolo e messaggio), `login`, `home` di `Modules/Fixcity/lang/{it,en,de,es}/error_pages.php`;
- il **messaggio dell'eccezione non viene mai mostrato** al cittadino;
- se la vista fallisce (`catch (Throwable)`) il gestore ritorna `null` e vale la pagina standard: un errore dentro la pagina d'errore non nasconde l'errore originale.

## Alternative scartate

| Alternativa | Perché no |
|---|---|
| `resources/views/errors/403.blade.php` e `419.blade.php` globali | Valgono per ogni richiesta HTML di ogni area: non permettono di escludere API, Livewire, back office e asset. La lingua della richiesta va impostata a mano anche qui (il 404 ne ha già bisogno), e un file di vista non può farlo |
| Un secondo gestore con la propria copia dell'elenco di percorsi | Due elenchi si disallineano: la prima volta che un'area nuova va esclusa se ne dimentica uno. Le closure condivise tengono il filtro in un posto solo |

## Confini deliberati

- **500, 503 e il 429 di login e registrazione restano la pagina standard.** I 500 vanno tenuti minimali: il layout potrebbe essere la causa dell'errore.
- **Il 419 di una richiesta Livewire non passa da qui**: il frontend di Livewire gestisce già la scadenza della sessione.
- axe-core sulla pagina 419 non è ancora stato eseguito: confluisce nell'accessibilità residua (DCP-452).

## Conseguenze

- Un'area che non deve avere pagine d'errore del cittadino si esclude **una volta sola**, in `$isCitizenHtmlRequest`, e vale per 404, 403 e 419.
- `bootstrap/app.php` è un file condiviso: chi lo tocca deve rieseguire `ThemedNotFoundPageTest` e `ThemedAccessErrorPagesTest`.
- Dato che il messaggio dell'eccezione non è mostrato, la causa di un 403 si legge solo nei log del server.

## Verifica

- `Modules/Fixcity/tests/Feature/Pages/ThemedAccessErrorPagesTest.php` (8 test) verdi. Con la mappa 403/419 svuotata i test rossi sono **7 su 8**; resta verde solo il caso API/JSON, che è giusto così.
- `ThemedNotFoundPageTest` 7/7, invariato.
- 419 vero sul dev: `POST /it/auth/logout` e `/de/auth/logout` senza token danno la pagina a tema con titolo, header e footer; a 375 e 1440 px senza overflow; pulsanti a 48 px.
- PHPStan 0 errori e Pint passed sui file toccati.

## Come si revoca

Togliere il gestore da `bootstrap/app.php` riporta la pagina standard di Laravel per 403 e 419 senza altri effetti: la vista e le chiavi lang restano inutilizzate. Prima di toglierlo va tolto o aggiornato `ThemedAccessErrorPagesTest`, che diventa rosso.
