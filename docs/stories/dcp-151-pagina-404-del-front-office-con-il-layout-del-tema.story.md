---
id: dcp-151
title: "DCP-151: pagina 404 del front office con il layout del tema, in quattro lingue"
type: story
epic: dc-parity-v2
status: done
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "pagina 404 not found layout tema lingua bootstrap app.php exceptions render NotFoundHttpException soft-404 DCP-126 error_pages"
related: [dcp-126, dcp-149]
---

# DCP-151: il 404 del cittadino

## Fase BMAD
Dev + verifica. Nasce da DCP-126 (i percorsi inesistenti ora rispondono 404 vero): una pagina d'errore nuda sarebbe stata il nuovo difetto piu' visibile.

## Difetto misurato (2026-10-14 22:40 UTC)
`/it/zzz/yyy`, `/it/tickets/999999`, `/it/zzz-pagina` rispondevano 404 con la pagina standard di Laravel: titolo "Not Found", nessun header, nessun footer, nessuna lingua, nessuna strada per andare avanti. Per un portale di un ente pubblico e' la pagina che un cittadino vede piu' spesso quando sbaglia un indirizzo o segue un link vecchio.

## Correzione
- Vista `Modules/Fixcity/resources/views/errors/not-found.blade.php`: layout del tema (`x-layouts.app`), `data-dc` per lo scope Design Comuni (fuori scope il CSS legacy toglie il bordo a ogni `.btn` e i pulsanti secondari sembravano link: visto nello screenshot e provato con `explain-style`), `h1`, messaggio, tre strade nella lingua della richiesta: home, elenco segnalazioni, nuova segnalazione. Nessun `href="#"` nel contenuto.
- Testi `Modules/Fixcity/lang/{it,en,de,es}/error_pages.php` (nessun testo nella vista).
- Gestore `NotFoundHttpException` in `laravel/bootstrap/app.php` (modifica ancorata accanto a quello del 429): lingua dal primo segmento del percorso, risposta `fixcity::errors.not-found` con stato 404. **Esclusioni**: richieste che chiedono JSON, `api/*`, `livewire/*`, `themes/*`, `storage/*`, `data/*`, `*/assets/*`, `fixcity/*`, `*/admin/*` restano alla risposta standard. **Rete di sicurezza**: se il rendering lancia, il gestore ritorna `null` e Laravel usa la sua pagina (un errore del layout non diventa un 500).

## Prove
- curl: `/{it,en,de,es}/zzz/yyy` -> 404 con header Design Comuni e titolo nella lingua ("Pagina non trovata", "Page not found", "Seite nicht gefunden", "Página no encontrada"); `/it/tickets/999999`, `/it/tickets/abc`, `/it/zzz-pagina`, `/zzz` -> 404 col tema; `/api/zzz`, `/livewire/zzz`, `/themes/Sixteen/zzz.css`, `/data/zzz.json`, `/fixcity/admin/zzz`, `/it/assets/zzz.svg` -> 404 standard; `Accept: application/json` -> JSON.
- Pest `Modules/Fixcity/tests/Feature/Pages/ThemedNotFoundPageTest.php`: 7 test, 41 asserzioni. **6 su 7 falliscono** ripristinando il vecchio `bootstrap/app.php` (resta verde solo il test su API/JSON, giusto cosi'), tutti verdi con il gestore. Insieme a `UnknownTwoSegmentPageTest`: 18 passati.
- axe-core (`a11y-page-probe.mjs`, wcag2a/2aa/21a/21aa) sul 404 in it, en, de, es: 0 nodi serious/critical.
- Screenshot a 375 e 1440 px: header e footer Design Comuni, card centrata, pulsante primario pieno e due secondari con bordo, bersagli di 48 px a 375 px, nessun overflow.
- PHPStan (config unica) a zero su `bootstrap/app.php` e sul test. Sentinelle `/it`, `/it/tickets`, `/it/tests/homepage`, `/it/tickets/confirmation`, `/it/tests/permessi-area-personale` a 200 dopo `view:clear`.
- Backup: `scratchpad/dcp124-backup/bootstrap-app.before151`.

## Errore mio lungo il percorso
Per la prova "rosso senza correzione" ho sostituito per qualche minuto `bootstrap/app.php` con la copia precedente, con un altro agente che sta modificando lo stesso file (registrazione di `SecurityHeaders`). Ho controllato dopo: la copia di backup conteneva gia' la sua modifica e la differenza e' solo il mio gestore; nessuna modifica persa. Per i file condivisi la prova inversa va fatta su una copia del test o con `git stash` mirato, non sostituendo il file vivo.

## Non coperto
- 403, 419, 500, 503 e il 429 di login/registrazione restano la pagina standard (il 429 di tracciamento e conferma e' gia' a tema, DCP-149). I 500 vanno tenuti minimali di proposito (il layout potrebbe essere la causa).
- `/it/api/tickets/geojson` risponde 200 con la pagina del tema invece di 404 (PP-09 di DCP-104): non toccato.
- Nessun suggerimento "forse cercavi": serve un indice delle pagine e una ricerca.
