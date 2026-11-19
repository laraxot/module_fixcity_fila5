---
title: "STORY-013 — Preferenze email per le segnalazioni"
id: STORY-013
author: BMAD
status: implemented
priority: high
type: story
module: Fixcity
tags: [bmad, fixcity, notification, preferences, citizen]
created: 2026-09-27
updated: 2026-09-27
qmd: "ticket notification email preferences profile citizen Fixcity"
issues:
  - https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
  - https://github.com/laraxot/base_fixcity_fila5/discussions/392
related:
  - ../gap-analysis.md
  - ../workflows/actor-citizen.md
  - ../workflows/actor-system.md
  - STORY-003-system-notification.md
---

# Preferenze email per le segnalazioni

## Obiettivo

Il cittadino autenticato controlla le email operative FixCity dal percorso
`Area personale → Impostazioni`. Le notifiche in-app restano disponibili; le
email sono inviate soltanto a indirizzi verificati e secondo la preferenza scelta.

## Criteri di accettazione

- [x] La pagina impostazioni richiede autenticazione e mostra la preferenza email FixCity.
- [x] Il collegamento alle preferenze è disponibile dall'elenco pratiche, dalla pagina seguite e dal centro notifiche.
- [x] La preferenza è salvata nelle preferenze del profilo senza cancellare chiavi di altri moduli.
- [x] Il valore predefinito resta abilitato per preservare il comportamento esistente; l'utente può disabilitarlo e riabilitarlo.
- [x] Con email disabilitata, cambi stato e assegnazioni mantengono il canale in-app ma non inviano email.
- [x] Email non verificata o vuota non abilita il canale mail, qualunque sia la preferenza salvata.
- [x] Il test copre accesso guest, persistenza, rendering e destinatari per tutti i casi.
- [x] Le etichette e gli stati di salvataggio sono localizzati e accessibili da tastiera.

## Confini

Push, digest, scelta per singolo evento, retry, ricevute SMTP e tracking delivery
sono fuori da questa story e restano gap distinti.

## Piano tecnico

Vedi [piano di sviluppo](STORY-013-ticket-notification-preferences.dev.md).

## Evidenza

Test mirati: 12 test / 37 asserzioni. Suite Fixcity: **356 test / 1.458
asserzioni**, PHPStan `analyse Modules` senza errori, Pint mirato e Blade cache
passati. La verifica globale `pint --test` resta rossa per molti file legacy e
parse error preesistenti in Sixteen, non toccati da questa story. La QA browser
visuale non è stata eseguita in questo ambiente.
