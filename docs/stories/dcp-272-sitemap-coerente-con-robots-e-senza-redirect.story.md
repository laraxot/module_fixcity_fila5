---
title: "DCP-272 - Sitemap coerente con robots.txt e senza reindirizzamenti, pronta per Search Console"
type: story
status: done
updated: 2026-10-26
tags: [docs, fixcity, seo, sitemap, robots, search-console, bmad, ponytail]
qmd: "dcp 272 sitemap.xml robots.txt search console URL bloccati reindirizzamento 301 tickets segnalazioni auth login register"
related:
  - ../../../../Themes/Sixteen/docs/stories/dcp-166-filament-blueprint-principi.story.md
  - ./dcp-200-registro-unico-delle-decisioni-aperte.story.md
---

# DCP-272 - Sitemap coerente con robots.txt e senza reindirizzamenti, pronta per Search Console

**Stato:** done (le due condizioni misurate sono coperte da test; il report vero di Google non e' verificabile da qui). Richiesta dell'utente (2026-10-26): ha incollato la guida del report Sitemap di Search Console, senza altre parole.
Lettura: «la nostra sitemap supererebbe quel report? Cosa manca per inviarla?». Nulla viene inviato a Google.

**Aggiornamento 2026-10-26:** la classe che costruisce la sitemap ora sta in `Modules/Seo` (`Modules\Seo\Actions\Sitemap\BuildSitemapAction`),
non in Fixcity; le pagine di dettaglio (articoli, eventi, segnalazioni) sono in [DCP-273](../../../Seo/docs/stories/dcp-273-sitemap-con-le-pagine-di-dettaglio-di-blog-ed-eventi.story.md).

## Cosa segnala il report e cosa risultava

Il report Sitemap di Search Console segnala, tra l'altro, «La Sitemap contiene URL bloccati dal file robots.txt» e «URL non seguiti» (un URL
della sitemap che risponde con un reindirizzamento). Misure sul nostro stato:

- `robots.txt` chiudeva `/it/...` ma non le altre lingue: mancavano 14 righe (`/en|de|es/learn`, `/dashboard`, `/artisan-commands-manager`,
  `/de|es/bootstrap-italia-showcase`, `/es/api`, `/es/area-personale`, `/es/auth`). Il commento di `robots.txt` lo dice.
- La sitemap elencava `segnalazioni.index`, alias Design Comuni di `tickets` che risponde 301: un «URL non seguito».
- `auth.login` e `auth.register` sono pagine per ospiti (senza middleware `auth`) ma `robots.txt` chiude `/{lang}/auth`: URL in sitemap e
  bloccato da robots, il caso esatto del primo avviso.

## Causa

Nessun test confrontava la sitemap con `robots.txt`: la coerenza tra i due non era una proprieta' verificata. I tre test sotto la rendono tale.

## Decisioni

- Fuori sitemap: pagine di prova (`tests.`), pagine `auth.`, pagine dietro `auth`, `tickets.confirmation` (usa `?code`), `segnalazione-dettaglio`
  (e' uno schema) e `segnalazioni.index` (301). Solo l'URL finale entra in sitemap.
- `robots.txt` chiude le stesse aree in tutte le lingue; le righe delle altre lingue si derivano da quelle `/it/`.
- `Sitemap:` resta non dichiarato in `robots.txt`: il file e' statico, identico in sviluppo e in produzione, e un URL assoluto sbagliato e' peggio
  di nessuno. La sitemap si invia dal report di Search Console sulla proprieta' del dominio vero.
- Niente `lastmod` (non c'e' una data affidabile per pagina, DCP-167).

## Verifica

Nel file `Modules/Fixcity/tests/Feature/Pages/SeoLinksTest.php` (tre guardie permanenti, 2026-10-26): «lists no url that robots.txt blocks»,
«closes the same private and development areas in every language», «lists only final urls, never one that redirects». Ultima esecuzione sul DB
di prova: `SeoLinksTest` + `TicketPublicDetailPageTest` **33 passed (419 assertions)**.

## Non verificato

- Il report vero di Search Console: il server MCP `gsc` non e' connesso e nulla e' stato inviato a Google. «Supererebbe il report» e' una
  previsione sulle due condizioni misurate sopra, non un esito di Google.
- Il dominio di produzione e la sitemap servita da li'.
- Il test dei reindirizzamenti controlla solo gli URL `/it`; per en, de, es il comportamento e' lo stesso per costruzione (stesso template), non provato URL per URL.
