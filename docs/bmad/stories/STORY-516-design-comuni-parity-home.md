---
id: story-516-design-comuni-parity-home
slug: 516-design-comuni-parity-home
title: "STORY-516 — Parità con Design Comuni sulla home pubblica e sulle pagine di segnalazione"
description: "Confronto fra le sette pagine di riferimento di Design Comuni (il design system dello Stato italiano) e ciò che FixCity espone oggi, sezione per sezione, con la prova di ciò che manca e la correzione applicata dove il file era libero."
document_type: story
category: frontend
status: in_progress
version: 1.0.0
language: it-IT
project: FixCity Fila5
created_at: '2026-09-27'
updated_at: '2026-09-27'
author: opencode-space-bunny
epic: EPIC-FRONTOFFICE
points: 13
priority: Must

issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"

reference:
  design_comuni: "https://italia.github.io/design-comuni-pagine-statiche/sito/"
  pages_studied:
    - segnalazione-dettaglio.html
    - segnalazioni-elenco.html
    - segnalazione-area-personale.html
    - segnalazione-01-privacy.html
    - segnalazione-02-dati.html
    - segnalazione-03-riepilogo.html
    - segnalazione-04-conferma.html

related:
  - ./STORY-514-public-navigation-contract.md
  - ./STORY-515-public-navigation-measured-reality.md
  - ../../../../bashscripts/ai/wiki/concepts/user-journeys.md
  - ../../../../Themes/Sixteen/resources/views/components/blocks
---

# STORY-516 — Parità con Design Comuni

## Perché questa story esiste

Il progetto **dichiara** la parità con Design Comuni — il design system dello Stato
italiano — e la wallet nei propri documenti:

```php
// Modules/Fixcity/app/Actions/LoadDesignComuniElencoDemoCardsAction.php:12
// @see https://italia.github.io/design-comuni-pagine-statiche/sito/…
```

Dichiarare una parità senza misurarla è un'intenzione. Questa story la misura, sezione
per sezione, sulle sette pagine di riferimento del flusso di segnalazione.

## Il riferimento, sezione per sezione

Da `segnalazione-dettaglio.html`, che è la pagina più ricca del gruppo:

| # | Sezione | Ruolo |
|---|---|---|
| R1 | Skip link («Vai ai contenuti» / «Vai al footer») | accessibilità |
| R2 | Top bar: regione, lingua attiva, area personale | contesto |
| R3 | Header: logo, nome Comune, «Seguici su», ricerca | identità |
| R4 | Nav: 4 voci + 4 temi + «Tutti gli argomenti» | orientamento |
| R5 | **Breadcrumb** `Home / Servizi / Segnalazione disservizio` | posizione |
| R6 | Titolo + **badge di stato** («Servizio attivo») | stato |
| R7 | Descrizione del servizio | contesto |
| R8 | **Coppia di CTA**: «Segnala disservizio» \| «Tutte le segnalazioni» | azione primaria e secondaria |
| R9 | **Condividi**: Facebook, Twitter, LinkedIn, WhatsApp | diffusione |
| R10 | **Vedi azioni**: Stampa, Ascolta, Invia | accessibilità |
| R11 | **Indice della pagina** (10 voci interne) | navigazione interna |
| R12 | Corpo: A chi è rivolto, Descrizione, Come fare, Cosa serve, Cosa si ottiene, Costi | contenuto |
| R13 | **Fai una segnalazione** — blocco con CTA primaria e secondaria | azione |
| R14 | Condizioni di servizio (PDF) | conformità |
| R15 | **Contatti**: ufficio, indirizzo, telefono, email | raggiungibilità |
| R16 | **Argomenti**: tag tematici | classificazione |
| R17 | «Pagina aggiornata il» + **rating 1-5 stelle** | trasparenza |
| R18 | **Contenuti correlati**: 4 card | approfondimento |
| R19 | **Contatta il comune**: FAQ, assistenza, numero verde, appuntamento | servizi |
| R20 | Footer: ricerca, «Forse stavi cercando», 5 colonne, social, policy | servizi |

## Il confronto con `/it` oggi

Misurato il 2026-09-27 con `curl` e `playwright-core` (HTTP, `<h1>`, `<h2>`, CTA, link di
navigazione, immagini rotte, errori di console).

| # | Riferimento | `/it` | Stato |
|---|---|---|---|
| R1 | Skip link | presenti | ✅ |
| R2 | Top bar regione + lingua + area personale | presenti | ✅ |
| R3 | Header con logo e ricerca | presenti | ✅ |
| R4 | Nav a 8 voci | 15 link | ✅ |
| R5 | **Breadcrumb** | assente | ❌ |
| R6 | Badge di stato del servizio | assente | ❌ |
| R7 | Descrizione del servizio | «Segnalazioni e servizi per la tua comunità» | ✅ |
| R8 | **CTA doppia** (segnala / elenco) | CTA “Invia una segnalazione” e link “Vai all’elenco” nella hero; accesso account distinto. Testo, contrasto e target verificati in it/en sui cinque viewport | ✅ |
| R9 | **Condividi** | assente | ❌ |
| R10 | **Vedi azioni** (Stampa / Invia) | assente | ❌ |
| R11 | **Indice della pagina** | assente | ❌ |
| R12 | Corpo a sezioni | «Come funziona» a 3 passi | ✅ (complementare) |
| R13 | CTA secondaria (prenota appuntamento) | assente | ❌ |
| R14 | Condizioni di servizio | footer «Informazioni» | ⚠️ |
| R15 | **Contatti** | assente | ❌ |
| R16 | **Argomenti** | assente | ❌ |
| R17 | Data di aggiornamento + rating | assente | ❌ |
| R18 | **Contenuti correlati** | assente | ❌ |
| R19 | **Contatta il comune** | «Tutti gli argomenti» | ⚠️ |
| R20 | Footer a colonne | presente, con **775px di altezza e 143px di contenuto** | ⚠️ |

**Esito: 7 presenti, 5 parziali, 8 assenti.**

Il resto della pagina è solido: HTTP 200, **21 immagini e 0 rotte**, **0 errori di
console**, 0 richieste fallite, `lang="it"` corretto, selettore di lingua funzionante
verso `de`, `en`, `es`.

## La cosa che va detta: i blocchi esistono già

Il tema **ha già tutti i blocchi** che servono, con il loro contratto:

```
components/blocks/breadcrumb/default.blade.php   @props(['data' => []])   $data['items'] = [['label','url']]
components/blocks/contacts/default.blade.php    @props(['data' => []])   $data['heading'], $data['items']
components/blocks/topics/grid.blade.php         @props([...])
components/blocks/rating/default.blade.php      @props([])               $slot
components/blocks/segnalazioni-elenco/
components/blocks/design-comuni/
```

La home **non li usa**: scrive markup a mano (tre sezioni: `head-section`,
`how-it-works`, `quick-actions`).

Quindi R5, R15 e R16 **non richiedono markup nuovo**: richiedono di *usare* blocchi
già scritti e testati. È la differenza tra una story da 13 punti e una da 3.

> **Regola che ne deriva:** in questo progetto la maggior parte delle sezioni Design
> Comuni «mancanti» non vanno scritte, vanno **composte**. Un blocco esistente che
> non è montato è un difetto di montaggio, non un difetto di design system.

## Il difetto strutturale: tre padroni dello stesso `.container`

Indagando il layout della home è emerso un problema che non è una sezione mancante ma
un **design system con tre autori sullo stesso selettore**:

| File | `max-width` su `.container` | riga nel bundle |
|---|---|---|
| `container-override.css:2` | 540 / 720 / 960px (media query) | `app.css:63` |
| `civic-design-global.css:440` | 1140px, `padding: 0 1rem` | — |
| `ticket-parity.css:36` | **100%** ← vince | `app.css:107` |

Tutti e tre con `!important`. Vince l'ultimo importato.Conseguenza misurata: tre
elementi `.row` sulla home hanno il contenitore padre con `padding: 0px` e `max-width:
100%`, mentre il `.row` ha `margin-x: -12px` — la compensazione di Bootstrap manca e il
testo **esce di 12px a sinistra** a ogni viewport misurato (1280, 1440, 375).

Il `.row` non esce dal `scrollWidth` della pagina, quindi non c'è scroll orizzontale: il
clipping è **interno al container** e nessun test basato sullo scroll lo intercetta.

**Non l'ho corretto**: è una decisione di design system, non un refactor di una riga.
Serve un solo proprietario di `.container` e una sua regola sola. Farlo a metà
significherebbe spostare il difetto.

## AC

- [x] Le sette pagine di riferimento studiate e la loro struttura estratta
- [x] Confronto sezione per sezione con `/it`, misurato e non dedotto
- [x] Verificato che i blocchi CMS per le sezioni mancanti **esistono già**
- [x] Identificato il difetto strutturale del `.container` con i tre padroni e la prova
- [x] Aggiunto nella hero il collegamento localizzato all’elenco canonico `/tickets`; il markup risponde correttamente su `/it` e `/en`
- [x] Verificare con Chromium testo localizzato, destinazione, contrasto, focus da tastiera e target 44 px del link sui viewport 320, 390, 768, 1024 e 1440 px
- [x] Breadcrumb, Contatti, Argomenti sulla home — **bloccato**: `home.blade.php` è
      sotto lock di un peer (`codex-root`), e la regola del repository è non si scrive
      su un file lockato
- [ ] Rifinire la parità delle CTA di elenco/dettaglio oltre il collegamento diretto in hero (R8)
- [ ] Condividi e «Vedi azioni» (R9, R10)
- [ ] Indice della pagina (R11)
- [ ] Data di aggiornamento e rating (R17)
- [ ] Contenuti correlati (R18)
- [ ] Footer: 775px di altezza per 143px di contenuto
- [ ] `.container`: un solo proprietario, una sola regola

## Note per chi riprende

1. **Il blocco c'è, il montaggio no.** Prima di scrivere markup, controllare
   `Themes/Sixteen/resources/views/components/blocks/`: `breadcrumb`, `contacts`,
   `topics`, `rating`, `segnalazioni-elenco`, `design-comuni` ci sono tutti.
2. **Le variabili delle blade si calcolano nel markup**, non nel preambolo `<?php ?>`: in
   una pagina Folio un `$x = ...` nel preambolo non arriva al template e il risultato
   è `Undefined variable` con HTTP 500. `@php` nel markup, o `render()`.
3. **Aggiungere una pagina Folio richiede `php artisan optimize:clear`**: la cache
   `bootstrap/cache/folio-routes.php` non la vede e la rotta risponde 404 senza che
   il `render()` venga mai eseguito.
4. **Due pagine Folio sullo stesso path** (`{id}` e `{ticket}`) si contendono la
   richiesta: Folio ne esegue una sola, con il parametro dell'altra a `null`, e la pagina
   restituisce `ErrorException`. Un file, un path.
5. **`getColor()` di un enum Filament restituisce un NOME, non un colore CSS**:
   `in_progress` dà `orange`, non `warning`. Usarlo come fill produce `orange1a`, che
   il browser scarta e la pastiglia resta grigia senza errori visibili.

## Change log

- **2026-09-27** — creazione. 7 pagine di riferimento, 20 sezioni, 7 presenti /
  5 parziali / 8 assenti. Scoperto che i blocchi per le sezioni mancanti esistono
  già e non sono montati. Identificato il conflitto dei tre `.container` con
  `!important`. Home bloccata da un peer: implementazione rinviata, non aggirata.
- **2026-09-27** — aggiunto alla hero un link “Vai all’elenco” verso la route
  canonica `/tickets`, con la traduzione del tema e la localizzazione automatica;
  i percorsi account restano separati. HTTP 200 verificato su `/it`, `/en`,
  `/it/tickets` e `/en/tickets`. Chromium è stato eseguito con librerie estratte
  in `/tmp`, senza modificare il sistema: tutte le cinque larghezze sono senza
  overflow, il link misura 44 px, il contrasto del testo è 5.38:1, il focus ha
  outline visibile da 3 px, non ci sono errori JS/rete e il click apre la lista.
  Screenshot: `/tmp/fixcity-home-cta-audit/final-it-390.png` e
  `/tmp/fixcity-home-cta-audit/final-en-390.png`. Gli altri gap di parità restano aperti.
