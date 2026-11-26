---
title: STORY-007 — Accesso guest senza flussi simulati
id: STORY-007
author: BMAD
status: in-progress
priority: must
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
tags:
- guest
- home
- routing
- ux
- fixcity
type: story
module: Fixcity
created: legacy
updated: '2026-09-26'
qmd: STORY 007 guest welcome flow FixCity BMAD story
---

# Obiettivo

Quando una persona apre il sito o prova a inviare una segnalazione senza autenticarsi,
vede solo funzioni effettive e viene indirizzata al flusso di creazione protetto.

## Criteri di accettazione

1. La pagina alias `/segnalazioni/create` conduce alla route canonica CMS `tickets.create`.
2. Il middleware configurato sulla pagina CMS richiede autenticazione e riporta l'utente al login localizzato.
3. Il guest non può creare record, né consultare dati privati tramite elenco, dettaglio o API.
4. Home ed elenco non presentano contenuti demo come segnalazioni reali; ogni CTA punta a una destinazione funzionante.
5. Il test copre i comportamenti di accesso guest effettivamente verificati; il
   redirect guest del wizard e l’assenza di persistenza restano da provare.
6. La UI è controllata su desktop e viewport mobile; su viewport stretti il nome
   del Comune è leggibile senza clipping; ogni limite ambientale viene registrato.
7. Elenco e mappa usano gli stessi dati pubblici live; filtri stato/tipo,
   paginazione e popup non espongono il codice capability nel payload guest.
8. Il CTA “Segnala un disservizio” in `/it/segnalazioni` apre `/it/tickets/create`;
   il modal dei dettagli resta riservato ai marker e non sostituisce il flusso.

## Riscontro attuale

- Il mock di `/segnalazioni/create` è stato rimosso e sostituito da un redirect alla pagina canonica.
- La pagina CMS `tickets.create` dichiara `auth` nel contenuto di `config/local/fixcity/database/content/pages/tickets.create.json`.
- La route attiva `/segnalazioni` è la pagina CMS `segnalazioni.index` con
  `TicketLayoutViewModel`: mappa alimentata da `/api/tickets/geojson`, aggregati
  live, filtri applicati anche alle card e paginazione server-side da 20 record.
- La pagina non mette il codice capability nello snapshot; l'endpoint dettaglio
  lo restituisce soltanto all'owner autenticato secondo la policy.
- Feature test coprono lista senza segreto, filtro stato e pagina successiva;
  la suite Fixcity completa gira con SQLite di test. Il test MySQL diretto
  continua a essere negato per credenziali placeholder.
- Verifica regressione CTA: 2 test / 9 asserzioni passati; il CTA compare una
  volta, punta alla route canonica e il modal dettaglio resta presente senza
  essere il target del CTA.
- Browser locale: CTA cliccato da guest e redirect finale a `/it/auth/login`,
  form password visibile e nessun errore JS. Lista HTTP 200, titolo “Segnalazioni”,
  un CTA e nessun overflow a 320/768/1.440 px.
- Header Sixteen corretto per viewport fino a 359 px: a 320 px il titolo brand
  usa 16 px e il suo scroll width coincide col client width (95 px); a 360 px
  torna 18 px e resta interamente visibile (111 px). Build Sixteen completata.
- Esito suite completa aggiornato il 2026-09-27: 358 test / 1.466 asserzioni.
- Status `in-progress`: restano wizard autenticato, persistenza reale, controllo
  accessibilità e flusso cittadino → PA in staging. Il browser di sviluppo non
  sostituisce il gate su dati e servizi di un ambiente deployato.
