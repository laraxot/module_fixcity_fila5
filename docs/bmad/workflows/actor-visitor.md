---
title: "BMAD actor — Visitatore non autenticato"
type: workflow
status: active
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, actor, visitor, guest, locale, fixcity]
qmd: "FixCity visitor guest public listing tracking ticket create login localized actor"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../actor-flows.md
  - actor-citizen.md
  - ../gap-analysis.md
  - ../../wiki/concepts/user-journey-map.md
---

# Attore: visitatore non autenticato

## Percorso disponibile

1. Apre la home o l'elenco pubblico e legge le segnalazioni pubblicabili.
2. Filtra la lista e passa tra vista elenco e mappa.
3. Può tracciare una segnalazione solo con il codice pubblico ricevuto; il codice
   bearer non viene mostrato nell'elenco e l'ID sequenziale non autorizza accesso.
   Il percorso canonico è `/{locale}/tickets/track?code={codice}`: codice valido
   mostra stato e timeline pubblica, codice errato mantiene il form con errore
   collegato al campo. Il tracking limita le ricerche guest a 10 al minuto; al
   superamento mostra una pagina 429 nella lingua corrente con istruzione e link
   per tornare alla ricerca. L'header `Retry-After` resta presente.
4. Se seleziona «Invia una segnalazione», il wizard richiede autenticazione.
   Il redirect mantiene la lingua (`/it` o `/en`) e conserva la destinazione
   richiesta per il ritorno dopo il login.
5. Dopo l'accesso prosegue nel flusso autenticato del [cittadino](actor-citizen.md).

## Limiti attuali

- Segnalazione anonima non disponibile; non esporre questo come scelta utente.
- Contatti dell'ente, informativa privacy pubblica, note legali e accessibilità
  non sono configurati: gap [G-26](../gap-analysis.md). `/privacy` mostra il
  503 localizzato finché il tenant non pubblica il proprio Markdown; il wizard
  non raccoglie né invia segnalazioni senza policy.
- Il passaggio login → wizard va completato e verificato nel browser con una
  sessione account controllata, non basta il test del solo redirect. La pagina privacy
  è ora coperta da test HTTP/Livewire (503 senza policy, rendering Markdown sanificato
  con policy di test); la verifica browser responsive della route è ancora pending.

## Verifiche

Pest copre il redirect guest IT/EN e il 401 JSON. Chromium deve coprire il click
da CTA, la pagina login localizzata, il ritorno al wizard dopo autenticazione e
il layout a 320/768/1440 px. Pest copre anche codice tracking valido/errato,
isolamento del codice bearer, traduzioni IT/EN e limite 10/minuto; la schermata
429 responsive resta da verificare nel browser prima del rilascio. Nel runtime attuale Chromium non parte per librerie di sistema assenti (`libatk-1.0`, `libatk-bridge2.0`, `libasound`, `libXdamage`, `libatspi`); non dichiarare svolte nuove prove visuali.
