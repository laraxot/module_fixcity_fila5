---
id: dcp-171
title: "DCP-171: vocabolari controllati della PA per i tipi di segnalazione"
type: story
epic: dc-parity-v2
status: needs-data
priority: P3
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "vocabolari controllati ontologie pa tipi di segnalazione ticket tipo categorie schema.gov.it dati.gov.it linee guida design 4.3.7 interoperabilita"
related: [dcp-163]
---

# DCP-171: vocabolari controllati della PA per i tipi di segnalazione

## Lacuna e prova (2026-10-14)
Requisito 4.3.7 (Linee guida di design della PA, capitolo 4 «Requisiti» (versione corrente, letto il 2026-10-14)): usare ontologie e vocabolari controllati standard della PA. Misura: i tipi di segnalazione sono un enum interno (`TicketTypeEnum`) senza corrispondenza con alcun vocabolario; l'API pubblica espone i valori interni.

## Norma
Linee guida di design 4.3.7 (DEVONO), con rinvio alle linee guida di interoperabilita'. Quali vocabolari siano pertinenti (per esempio quelli del catalogo nazionale di schemi e vocabolari controllati): NON VERIFICATO, da leggere sulla fonte.

## Requisiti
1. Studio dei vocabolari applicabili a segnalazioni e servizi del Comune e scelta, con motivazione.
2. Tabella di corrispondenza dai tipi interni agli identificatori del vocabolario, nel modello o in configurazione, senza cambiare i valori che i cittadini vedono.
3. Esposizione degli identificatori nell'API dei dati pubblici.

## Criteri di accettazione
- [ ] Documento di scelta con fonte letta e data.
- [ ] Test: ogni tipo interno ha una corrispondenza; l'API la espone.

## Dipendenze e priorita'
Dati e scelta dell'ente (needs-data). P3: non blocca la pubblicazione, completa l'interoperabilita'.
