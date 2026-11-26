---
title: "Percorso pubblico del logo FixCity"
type: decision
status: accepted
created: 2026-11-23
updated: 2026-11-26
module: Fixcity
decision-makers: [team Laraxot]
---

# Percorso pubblico del logo FixCity

## Contesto

Il pannello FixCity e i metatag costruiscono l'URL pubblico del logo a partire dalla configurazione `metatag` (`logo_img`, `logo_square`, `logo_footer_img`, `logo_header`,
`logo_header_dark`). Il modulo pubblica `logo.svg` in `public/svg` (`Modules/Fixcity/public/svg/logo.svg`) e non ha una cartella `public/images`; `public_html/assets/fixcity`
e' un symlink a `laravel/Modules/Fixcity/public`. Quale percorso `modulo::...` devono dichiarare le configurazioni perche' il logo si carichi davvero?

## Driver della decisione

- Il percorso dichiarato deve coincidere con un file che esiste nella cartella pubblica del modulo: un logo che risponde 404 e' visibile a ogni visitatore.
- Un solo asset per il logo, senza copie da tenere allineate.
- Gli asset dei moduli si servono dal symlink `public_html/assets/<modulo>` (regola di progetto sui moduli: `public_html` e' la document root).

## Alternative scartate

- Mantenere `fixcity::images/logo.svg`: produceva una richiesta 404, perche' `public/images` non esiste.
- Aggiungere una copia in `public/images`: duplica l'asset e contraddice la cartella pubblica gia' esistente.

## Decisione

Le configurazioni FixCity usano `fixcity::svg/logo.svg` per tutti i riferimenti al logo. Il percorso coincide con l'asset effettivamente pubblicato dal modulo e con il symlink
`public_html/assets/fixcity`, quindi l'URL pubblico e' `/assets/fixcity/svg/logo.svg`.

## Conseguenze

- Buono: il logo del pannello e dei metatag usa un solo asset pubblico.
- Cattivo: un nuovo file di configurazione `metatag` (per lingua o per tenant) che dimentica la decisione riporta il 404 senza errori di build; nessun controllo automatico lo impedisce oggi.
- Le verifiche browser devono controllare che `/assets/fixcity/svg/logo.svg` risponda con MIME `image/svg+xml`.

## Conferma

Nessuna conferma automatica oggi: `Modules/Fixcity/tests/Unit/DesignComuniAssetsTest.php` copre l'URL degli asset di Design Comuni, non i percorsi del logo FixCity.
Proposta: un test Pest in `Modules/Fixcity/tests/Unit` che legge ogni `config/**/metatag.php` del modulo e del tenant e verifica che ogni valore `fixcity::...logo` punti a un file esistente in
`Modules/Fixcity/public`.

Controllo manuale, da `laravel/`:

```bash
test -f Modules/Fixcity/public/svg/logo.svg && echo presente
rg -n "fixcity::images/" Modules/Fixcity/config config/local/fixcity
```

Il secondo comando non deve trovare nulla. Oggi trova 20 righe in 4 file (vedi Stato attuale).

## Stato attuale

Il codice contraddice in parte la decisione. `Modules/Fixcity/config/metatag.php:21-26` e `config/local/fixcity/metatag.php:21-26` usano `fixcity::svg/logo.svg`, ma le copie per
lingua `Modules/Fixcity/config/lang/{it,en}/metatag.php` e `config/local/fixcity/lang/{it,en}/metatag.php` dichiarano ancora `fixcity::images/logo.svg` alle righe 19-21 e 23-24 (cinque voci per
file: `logo_img`, `logo_square`, `logo_footer_img`, `logo_header_dark`, `logo_header`; 20 righe in tutto, controllo `rg -n "fixcity::images/" Modules/Fixcity/config config/local/fixcity`).
Se la configurazione per lingua prevale su quella generale, il logo resta sul percorso che questa decisione ha scartato. La correzione sta nei file di configurazione, fuori dallo scopo di una
passata sui documenti.
