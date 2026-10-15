---
id: dcp-157
title: "DCP-157: soddisfazione dell'utente e pubblicazione dei risultati (CAD art. 7)"
type: story
epic: dc-parity-v2
status: needs-decision
priority: P2
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "soddisfazione utente valutazione gradimento cad art 7 comma 3 rating ticket pubblicazione risultati urp"
related: [dcp-112]
---
# DCP-157: soddisfazione dell'utente e pubblicazione dei risultati (CAD art. 7)

## Lacuna e prova (matrice del 2026-10-13, righe N-28 e I-18 (parziale))
Il cittadino puo' valutare un ticket risolto (`GetCitizenRatingAggregateAction`, modulo Rating), ma il risultato non e' pubblicato in forma aggregata e nessuna pagina dice come l'ente usa le valutazioni. Il CAD chiede che gli utenti dei servizi in rete possano esprimere soddisfazione e che i dati siano pubblicati.

## Norma
CAD (D.Lgs. 82/2005) art. 7 c. 3; L. 150/2000 art. 8 c. 2 lett. d). Righe N-28 e I-18 della matrice.

## Requisiti
1. Pagina pubblica con le valutazioni **aggregate** (media, numero, periodo), mai i singoli giudizi con dati personali.
2. Soglia minima di valutazioni per mostrare un dato (evita di identificare una persona con 1 voto) e periodo configurabile.
3. Testo dell'ente su come le valutazioni vengono usate.

## Criteri di accettazione
- [ ] La pagina esiste in 4 lingue, ha test sulla soglia e non espone autore ne' testo libero.
- [ ] Il valore mostrato coincide con `GetCitizenRatingAggregateAction`.

## Dipendenze e priorita'
Modulo Rating; decisione dell'ente sulla soglia e sul periodo. P2.
