---
id: design-comuni-guest-crawl-2026-09-27
title: "BMAD — audit guest multilingua e demo runtime"
description: "Matrice attesa/confronto/correzione/verifica della navigazione pubblica FixCity rispetto al catalogo Design Comuni."
document_type: verification
category: frontend
status: implemented
version: 1.0.0
language: it-IT
project: FixCity Fila5
created_at: '2026-09-27'
updated_at: '2026-09-27'
author: opencode-space-bunny
references:
  design_comuni_index: "https://italia.github.io/design-comuni-pagine-statiche/index.html"
  design_comuni_services: "https://italia.github.io/design-comuni-pagine-statiche/servizi/index.html"
  issue: "https://github.com/laraxot/base_fixcity_fila5/issues/522"
  discussion: "https://github.com/laraxot/base_fixcity_fila5/discussions/523"
---

# Verifica BMAD: percorso pubblico guest

## Atteso

Un visitatore non autenticato deve poter capire il servizio, consultare news,
amministrazione, servizi, categorie, segnalazioni e privacy; deve poter aprire
login/registrazione e ricevere un redirect locale quando una funzione richiede
autenticazione. Le pagine devono essere accessibili, responsive, prive di link
placeholder e disponibili in IT/EN/DE/ES.

## Confronto e correzioni

- `/services` ora espone il catalogo e porta alla scheda reale
  `/services/report-issue`, senza inventare contatti o servizi comunali.
- `/lista-categorie` usa una pagina Folio effettiva con categorie localizzate e
  link funzionanti.
- `/segnalazioni/create` conserva la lingua nel redirect verso il wizard canonico.
- privacy e conferma usano cataloghi locale-specifici e data bag runtime.
- il server demo serve gli asset dal `public_html` sibling quando il document root
  è `laravel`, con MIME JavaScript esplicito; questo elimina falsi errori visuali.

## Verifica eseguita

Crawler Playwright locale, Chromium, viewport mobile, quattro lingue, rete,
console, overflow orizzontale, chiavi raw `pub_theme::`/`fixcity::`, status HTTP
e screenshot. Sono stati verificati home, servizi, dettaglio servizio, categorie,
tickets, tracking, news, amministrazione, privacy, auth e redirect guest.

Risultato: tutte le rotte canoniche sono 200 (o redirect guest previsto), nessuna
anomalia UI/i18n, nessun errore JavaScript sulle rotte canoniche e nessuna richiesta
asset fallita. I 404 residui sono solo percorsi inesistenti o superfici legacy
intenzionalmente bloccate.

## Gate residuo esplicito

La suite Pest che usa il checkout MariaDB non può autenticarsi con le credenziali
placeholder presenti nell’ambiente (`1045`); non sono stati modificati grant o
segreti. La regressione applicativa è stata verificata con seed demo, view cache,
PHPStan e crawler browser.
