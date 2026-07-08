# Segnalazione Header Navbar Green Rule

## Regola Fixcity

Il flusso segnalazione usa l'header del tema Sixteen. Fixcity non deve correggere i colori header nel Blade del wizard.

La navbar con voci:

- Amministrazione
- Novita'
- Servizi
- Vivere il Comune
- Iscrizioni
- Estate in citta'
- Polizia locale
- Tutti gli argomenti

deve ereditare il comportamento site-wide del componente header Sixteen: sfondo verde come logo/slogan, link bianchi.

## Responsabilita

- Fixcity: schema wizard, step, stato, submit, contenuti segnalazione.
- Sixteen: header, CSS Design Comuni, visual parity.

## False friend

Se la navbar appare blu non significa che il wizard sta sbagliando markup. Spesso e' Bootstrap Italia che applica background a `.it-header-navbar-wrapper .navbar` o a figli interni, coprendo il parent verde.

La correzione appartiene al tema e deve coprire tutta la catena visuale del componente header.

