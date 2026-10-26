---
id: IM-FIXCITY-005
title: "Seed demo realistico Blog/Eventi con Comment e Media"
type: story
status: ready-for-dev
updated: 2026-10-15
tags: [bmad, blog, eventi, seeder, factory, commenti, media, demo]
qmd: "story fixcity"
---

# Story

Come presentatore della demo FixCity voglio vedere articoli, eventi, immagini e
conversazioni realistiche, così posso mostrare il percorso informazione →
partecipazione senza Lorem Ipsum o record casuali.

## Decisione architetturale

Riutilizzare `Article` con `content_type=post|event` e il modulo Comment.
Nel prodotto il contenuto è **post** (agnostico). Parental `type` = solo STI.
Niente modulo Event in questa story.

## Acceptance criteria

- Il seeder scrive solo in `local`, `testing` o `demo`.
- Due esecuzioni non duplicano categorie, articoli, commenti o media demo.
- Nessun contenuto pubblico contiene Lorem Ipsum o placeholder.
- Sono presenti almeno 8 post `content_type=post` e 4 eventi `content_type=event` con titoli, descrizioni, CTA e date
  coerenti.
- Ogni contenuto ha immagine, alt text e credito/licenza dichiarati.
- Ogni contenuto ha commenti approvati; almeno una discussione ha una risposta.
- Esiste almeno un commento pending non esposto pubblicamente.
- Il dataset è riconoscibile come demo e non usa indirizzi email personali reali.
- Gli eventi futuri sono filtrabili da `content_type=event` oltre che dalla data.
- Il seeder produce un riepilogo con conteggi verificabili.

## Fuori scope

Iscrizioni, capienza, pagamenti, ricorrenze, esportazione ICS, newsletter,
tracking, modulo Event separato e traduzioni complete di tutte le lingue.

## File ownership previsto

- `laravel/Modules/Blog/database/factories/ArticleFactory.php`
- `laravel/Modules/Blog/database/seeders/DemoContentSeeder.php`
- eventuale catalogo in `laravel/Modules/Blog/database/seeders/Support/`
- test del modulo Blog per guardia, idempotenza e contenuto
- documentazione BMAD in questo dossier

## Nota BMAD

Story di supporto demo (Epic II). Non sblocca Epic III Filament/FO.
SSoT: [stories-index](../inbound-marketing/stories-index.md).
