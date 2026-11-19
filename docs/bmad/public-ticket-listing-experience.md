---
title: "Esperienza guest: elenco pubblico segnalazioni"
type: product-ux-spec
status: active
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, guest, public-list, ux, accessibility, i18n]
qmd: "FixCity guest public ticket list map cards filters create report what should show"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - workflows/actor-visitor.md
  - workflows/actor-citizen.md
  - workflows/07-ui-ux.md
---

# Elenco pubblico segnalazioni — esperienza attesa

## Scopo della pagina

La route localizzata `/it` o `/en` è la vetrina pubblica di FixCity: deve far
capire cosa è stato segnalato, dove si trova e come il visitatore può inviare una
segnalazione. Tutti i dati mostrati devono essere reali o marcati in modo evidente
come dati di demo; nessun testo deve simulare un atto o un intervento comunale.

## Cosa deve vedere una persona non autenticata

1. Header FixCity caricato e leggibile, selettore lingua funzionante e menu
   navigabile anche da tastiera e touch.
2. Titolo e descrizione localizzati che spiegano l'elenco; CTA primaria per
   creare una segnalazione. Se la policy privacy non è approvata, la CTA deve
   spiegare perché l'invio è sospeso e portare all'informativa disponibile.
3. Selettori mappa/elenco e filtri accessibili, con conteggi coerenti e uno stato
   vuoto chiaro quando non ci sono record.
4. Card di segnalazione con categoria, stato leggibile, descrizione breve, data e
   posizione quando autorizzata; apertura del dettaglio senza esporre dati
   personali o identificativi interni non previsti.
5. Footer con collegamenti localizzati funzionanti e senza recapiti o link fittizi.

## Lingue e responsive

Il cambio lingua mantiene la pagina equivalente in EN/IT. Ogni testo visibile,
placeholder, tooltip, aria-label e stato di caricamento deve provenire dal
catalogo owner, dai dati localizzati o da configurazione del tenant. Nessuna
frase italiana hardcoded nelle Blade tematiche. A 320, 390, 768, 1024 e 1440 px
non devono esserci scroll orizzontale, controlli sovrapposti o testo tagliato.

## Criteri di accettazione visiva

- CSS e JavaScript caricati senza dipendere da un Vite server non avviato.
- Nessun errore console, eccezione JS o risorsa essenziale 4xx/5xx.
- Un solo titolo H1, gerarchia heading corretta e focus visibile.
- Mappa, elenco, filtri e CTA usabili con touch e tastiera; etichette e stati
  esposti nell'albero accessibilità.
- Screenshot e verifica browser documentati per ogni viewport e locale; un test
  Pest non sostituisce il controllo visuale.

## Limiti dati della demo

I record con codice `DEMO-*` sono sintetici. La UI deve dichiararlo chiaramente
se li rende pubblici; non deve attribuire le segnalazioni a cittadini reali. Il
contenuto istituzionale, la privacy e i recapiti restano subordinati alla
configurazione approvata dal tenant.
