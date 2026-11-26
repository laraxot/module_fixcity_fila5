---
title: "Contratto redirect ticket tracking code"
type: contract
action: "Modules\\Fixcity\\Http\\Middleware\\RedirectTicketTrackCodeMiddleware"
module: Fixcity
status: accepted
tags: [contract, middleware, tracking, cms]
created: 2026-11-23
updated: 2026-11-26
---

# `RedirectTicketTrackCodeMiddleware`

Middleware (non Action) che porta i link storici delle ricevute `/{lang}/tickets/track/{code}` alla pagina di ricerca
`/{lang}/tickets/track?code={code}` (DCP-90). Lo dichiara il JSON CMS della pagina `tickets/track` (voce `middleware`), perche'
la pagina generica del tema `[container0]/[slug0]/[container1]` oscurava la vista Folio del modulo.

## Input

- Una richiesta Folio per `/tickets/track/{code}`, con il codice nel parametro di rotta `container1` e la lingua corrente di
  Laravel Localization.

## Output

- Un redirect localizzato verso `/tickets/track?code={code}` (il codice e' percent-encoded con `rawurlencode`).
- Le richieste senza `container1`, o con un valore non stringa o vuoto, proseguono senza redirect.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TRACK-REDIRECT-INV-1 | (gia' `TRACK-REDIRECT-1`) Il codice viene trasferito nella query `code`, mai lasciato nel path finale. | `TRACK-REDIRECT-INV-1` |
| TRACK-REDIRECT-INV-2 | (gia' `TRACK-REDIRECT-2`) Il redirect mantiene il prefisso della lingua corrente. | `TRACK-REDIRECT-INV-2` |
| TRACK-REDIRECT-INV-3 | (gia' `TRACK-REDIRECT-3`) Il middleware appartiene a Fixcity e nessun modulo condiviso (Cms compreso) dipende da `Modules\Fixcity`: il Cms lo carica solo come classe dichiarata nel JSON, tramite l'allowlist generica. Coperto da `Modules/Fixcity/tests/Arch/SharedPackagesDoNotDependOnFixcityTest.php` (un arch test per modulo, senza group). | — (coperto solo dall'arch test, senza group) |
| TRACK-REDIRECT-INV-4 | (gia' `TRACK-REDIRECT-4`) Altri percorsi a tre segmenti non vengono rediretti da questo middleware. | `TRACK-REDIRECT-INV-4` |
| TRACK-REDIRECT-INV-5 | `FixcityServiceProvider` aggiunge la classe a `cms.config.page_middleware_allowlist` senza togliere le voci gia' presenti; senza Fixcity l'allowlist resta quella di default del Cms. | — (non dimostrato) |

## Effetti collaterali

- Nessuna scrittura: legge il parametro di rotta e la lingua corrente, produce una risposta HTTP di redirect.
- Il `FixcityServiceProvider`, al boot, fonde la classe nell'allowlist del pipeline CMS (`PageSlugMiddleware` risolve ogni voce
  di `middleware` come alias o classe e la valida contro l'allowlist, `ValidatePageMiddlewareAction`).

## Errori

- `\LogicException` se il middleware successivo non restituisce una `Response` (invariante di tipo, mai vista in pratica).
- Nessun errore sul codice: non lo interpreta, non interroga il database e non decide se il ticket esiste. La validazione, la
  privacy e il freno ai tentativi sono della pagina di tracciamento e di `GetPublicTicketByCodeAction`.
- Limite noto: `TicketTrackPageJsonGuardTest` pretende `middleware` uguale a `["throttle:10,1"]` in ogni copia di `tickets.track.json`,
  ma la copia del tenant (`config/local/fixcity/database/content/pages/tickets/track.json`) ne ha due (`throttle:10,1` e questo
  middleware): per lettura statica il test non puo' essere verde. Va riallineato (Backlog test).

## Non fa

- Non viene importato da `Modules/Cms`.
- Non modifica il contenuto CMS.
- Non applica autorizzazioni o throttling: restano middleware distinti (`throttle:10,1` nel JSON, freno per indirizzo in
  `GetPublicTicketByCodeAction`).
- Non valida ne' normalizza il codice (nessun trim, nessun controllo di formato).
