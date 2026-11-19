---
title: Geolocation "usa la posizione corrente" non funziona
id: STORY-530
author: BMAD
status: open
priority: high
type: story
module: Geo
tags:
- bmad
- geo
- geolocation
- https
- browser-permission
- accessibility
created: 2026-10-14
updated: 2026-10-14
github_issue: "#530"
discussion: "https://github.com/laraxot/base_fixcity_fila5/discussions/530"
qmd: "geolocation usa posizione corrente non funziona browser secure context HTTPS permission Geo module"
---

## Descrizione del bug
Cliccando il pulsante "usa la posizione corrente" nel selettore coordinate della mappa (`geolocation.js`), la geolocalizzazione non si attiva o fallisce silenziosamente. L'utente non riceve un messaggio chiaro e la mappa non si centra sulla posizione attuale.

## Root cause analysis
1. **Contesto sicuro richiesto**: il codice verifica `!window.isSecureContext || !navigator.geolocation`. Se l'app gira su `http://` (non HTTPS), `isSecureContext` è `false` → il GPS viene rifiutato senza prompt all'utente.
2. **App configurata su HTTP**: `.env` indica `APP_URL=http://fixcity.local`; `.env.production.example` richiede `FORCE_HTTPS=true`, `APP_URL=https://...`.
3. **Permessi browser**: anche con HTTPS, se l'utente nega il permesso di geolocalizzazione, il codice entra nel ramo `error()` della callback ma il messaggio all'utente è neutro (`_locationError = ''`). Non c'è un invito esplicito a controllare i permessi del browser.
4. **Fallback attivato ma invisibile**: `fallBackToApproximate` viene chiamato sia su contesto non sicuro (`!auto`) sia su errore (`!auto` è `false` quando `auto=false` e il clic è esplicito → `explicitRequest = true`). In tal caso il fallback riporta alla vista iniziale ma non mostra un errore visibile (`_locationError` resta vuoto se il campo non è vuoto).

## Riferimenti
- Codice: `laravel/Modules/Geo/resources/js/components/map/controls/geolocation.js`
- Indagine permessi browser: `docs/investigation/geolocalizzazione-permesso-browser.md` (da creare/verificare)
- Configurazione HTTPS: `.env` (http://fixcity.local) vs `.env.production.example` (FORCE_HTTPS=true, APP_URL=https://fixcity.example.com)

## Il fix richiesto
- Aggiungere gestione esplicita dell'errore quando `window.isSecureContext` è `false`: mostrare un messaggio neutro che spiega che la funzione richiede HTTPS senza citare direttamente "HTTPS" all'utente (come da convenzione: "la posizione precisa non è disponibile — indica il punto sulla mappa").
- Quando il browser nega il permesso (`navigator.permissions` o `error.code === error.PERMISSION_DENIED`), attivare il fallback e impostare un flag `permissionDenied` sul contesto (`ctx`) in modo che il componente possa mostrare un invito esplicito a controllare le impostazioni del browser.
- Assicurarsi che `fallBackToApproximate` venga sempre chiamato con `explicitRequest` coerente (`!auto`) e che `ctx.requestUpdate?.()` sia invocato dopo l'aggiornamento di `ctx._locationError`.
- Documentare il requisito HTTPS nel file d'indagine.

## Stato attuale
- Codice `geolocation.js`: esiste con `fallBackToApproximate`, `requestGeolocation`, `renderButton`.
- `.env`: `APP_URL=http://fixcity.local` (HTTP, non sicuro per geolocation).
- `docs/investigation/geolocalizzazione-permesso-browser.md`: FILE NON ESISTENTE — va creato.
- `docs/investigation/investigation.md`: esiste ma non copre il caso geolocalizzazione.

## Accettazione
- [ ] Creazione `docs/investigation/geolocalizzazione-permesso-browser.md` con descrizione del requisito HTTPS, dei permessi browser e della logica di fallback.
- [ ] Verifica che `fallBackToApproximate` sia attivato in tutti i casi (`!isSecureContext`, `!navigator.geolocation`, errore permesso, timeout).
- [ ] Aggiunta gestione `explicitRequest` coerente e `requestUpdate` dopo `ctx._locationError`.
- [ ] Verifica che il codice non esponga mai messaggi espliciti su "HTTPS" o "permessi" all'utente finale (messaggio neutro, azione indicata: clic sulla mappa o ricerca).
