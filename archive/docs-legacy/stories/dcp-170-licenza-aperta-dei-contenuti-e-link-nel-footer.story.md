---
id: dcp-170
title: "DCP-170: licenza aperta dei contenuti e link alla licenza nel footer"
type: story
epic: dc-parity-v2
status: needs-decision
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "licenza aperta contenuti cc by 4.0 footer link licenza riuso dati aperti linee guida design 4.7 d.lgs 36 2006 riuso open data"
related: [dcp-145, dcp-162, dcp-163]
---

# DCP-170: licenza aperta dei contenuti e link alla licenza nel footer

## Lacuna e prova (2026-10-14)
Requisiti 4.7.1-4.7.2 (Linee guida di design della PA, capitolo 4 «Requisiti» (versione corrente, letto il 2026-10-14)): associare ai contenuti una licenza aperta e inserire il link alla licenza. Misura: 45 combinazioni su 48 non nominano alcuna licenza; nessun link alla licenza nel footer. Il modello delle note legali (DCP-162) ha il campo ma l'ente non ha scelto.

## Norma
Linee guida di design 4.7 (DEVE, «salvo diversa norma»), con rinvio al CAD e alle linee guida sul riuso; D.Lgs. 36/2006 sul riutilizzo dei documenti del settore pubblico (matrice N-36, NON VERIFICATO). La pagina non dice quale licenza.

## Requisiti
1. Decisione dell'ente sulla licenza dei contenuti e dei dati aperti (per esempio CC BY 4.0), in 4 lingue.
2. Voce del footer con il nome e il link alla licenza, in 4 lingue.
3. La stessa licenza in DCP-162 (sezione «Proprieta' intellettuale») e nell'API dei dati pubblici (`/api/tickets`, N-36).
4. Distinguere i contenuti dell'ente dai contenuti inviati dai cittadini (concessi all'ente per finalita' del servizio) e dalle foto con terzi.

## Criteri di accettazione
- [ ] Decisione registrata.
- [ ] `lg-design-scan.py`: «senza menzione licenza» da 45/48 a 0/48; il link risponde 200.
- [ ] Test: footer in 4 lingue con link non `#`.

## Dipendenze e priorita'
Decisione dell'ente (needs-decision). Complementa DCP-145 (licenze del software e dei font). P1: e' un DEVE.
