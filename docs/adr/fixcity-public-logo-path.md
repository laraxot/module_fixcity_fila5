---
title: "Percorso pubblico del logo FixCity"
type: adr
status: accepted
module: Fixcity
---

# Percorso pubblico del logo FixCity

## Stato

Accettato.

## Contesto

Il pannello FixCity costruisce l'URL pubblico del logo a partire dalla
configurazione metatag. Il modulo pubblica `logo.svg` nella directory
`public/svg`, mentre non pubblica una directory `public/images`.

## Decisione

Le configurazioni FixCity usano `fixcity::svg/logo.svg` per tutti i riferimenti
al logo. Il percorso coincide con l'asset effettivamente pubblicato dal modulo
e con il symlink `public_html/assets/fixcity`.

## Alternative scartate

- mantenere `fixcity::images/logo.svg`: produceva una richiesta 404;
- aggiungere una copia in `public/images`: duplica l'asset e contraddice la
  directory pubblica già esistente.

## Conseguenze

Il logo del pannello e dei metatag usa un solo asset pubblico. Le verifiche
browser devono controllare che `/assets/fixcity/svg/logo.svg` risponda con MIME
`image/svg+xml`.

## Verifica

La verifica è effettuata dal test asset del modulo e da una richiesta browser
alla pagina di login del pannello FixCity.
