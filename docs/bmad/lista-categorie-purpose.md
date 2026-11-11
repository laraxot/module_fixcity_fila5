---
title: "Lista categorie: contratto di prodotto"
type: bmad-product-contract
status: active
owner: Fixcity
tags: [bmad, front-office, categories, civic-reporting]
---

# Scopo

`/{locale}/lista-categorie` è la directory pubblica delle tipologie di problema
che FixCity può ricevere. Serve a orientare il cittadino prima del percorso di
segnalazione e a collegarlo all'elenco delle segnalazioni pubbliche.

La fonte dei dati è `Category`: solo categorie attive di primo livello vengono
pubblicate, con le eventuali sottocategorie ordinate. Il tema non inventa
servizi, conteggi o filtri: il catalogo espone esclusivamente dati già posseduti
dal modulo Fixcity.

## Confini

- Fixcity possiede modello, catalogo, stato attivo e gerarchia.
- Sixteen possiede markup, responsive layout e traduzioni del chrome.
- La pagina non sostituisce `/tickets`, non crea un endpoint e non monta una
  Resource Filament.
- Il blueprint Filament del tema resta applicabile come contratto di ownership:
  il tema compone la pagina e non possiede logica di dominio.

## Verifica

La pagina deve avere un solo `main`, un solo `h1`, un catalogo identificabile
`#category-directory`, CTA localizzate e nessuna chiave di traduzione visibile.
