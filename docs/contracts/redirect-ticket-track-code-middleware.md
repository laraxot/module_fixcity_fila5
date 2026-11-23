---
title: "Contratto redirect ticket tracking code"
type: middleware-contract
contract: Modules\\Fixcity\\Http\\Middleware\\RedirectTicketTrackCodeMiddleware
owner: Fixcity
status: accepted
updated: 2026-11-23
---

# `RedirectTicketTrackCodeMiddleware`

## Input

Una richiesta Folio per `/tickets/track/{code}`, con il codice nel parametro
`container1` e la lingua corrente fornita da Laravel Localization.

## Output

Un redirect localizzato verso `/tickets/track?code={code}`. Le richieste prive
di `container1` o con un valore vuoto proseguono senza redirect.

## Invarianti

| ID | Regola | Verifica |
|---|---|---|
| TRACK-REDIRECT-1 | Il codice viene trasferito nella query `code`, mai lasciato nel path finale. | `TicketTrackCodeRedirectTest` |
| TRACK-REDIRECT-2 | Il redirect mantiene il prefisso della lingua corrente. | `TicketTrackCodeRedirectTest` |
| TRACK-REDIRECT-3 | Il middleware appartiene a Fixcity e può essere caricato dal Cms come middleware dichiarato nel JSON, senza dipendenza inversa. | PHPStan + bootstrap modulo |
| TRACK-REDIRECT-4 | Altri percorsi a tre segmenti non vengono rediretti. | `TicketTrackCodeRedirectTest` |

Il `FixcityServiceProvider` registra la classe nell'allowlist generica del
pipeline CMS. In questo modo `Modules\Cms` non importa né conosce Fixcity:
un'installazione senza Fixcity mantiene il proprio allowlist di default.

## Effetti collaterali

Produce una risposta HTTP di redirect per i link storici delle ricevute.

## Errori

Non interpreta il codice, non interroga il database e non decide se il ticket
è valido: la pagina di tracking owner gestisce la validazione e la privacy.

## Non fa

- Non viene importato da `Modules/Cms`.
- Non modifica il contenuto CMS.
- Non applica autorizzazioni o throttling: questi restano middleware distinti.
