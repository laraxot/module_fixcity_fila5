---
id: dcp-159
title: "DCP-159: sicurezza organizzativa della PA: NIS2, notifica incidenti, misure minime AgID, RTD"
type: story
epic: dc-parity-v2
status: needs-data
priority: P2
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "nis2 d.lgs 138 2024 legge 90 2024 notifica incidenti 24 72 ore circolare agid 2 2017 misure minime rtd piano triennale amministratori di sistema log sei mesi"
related: [dcp-112, dcp-139, dcp-141, dcp-142]
---
# DCP-159: sicurezza organizzativa della PA: NIS2, notifica incidenti, misure minime AgID, RTD

## Lacuna e prova (matrice del 2026-10-13, righe N-19..N-23 e N-29)
Sei righe della matrice sono NON VERIFICATO e senza story perche' dipendono dall'ente, non dal codice: se un Comune e' soggetto a NIS2 dipende dalla popolazione e dal settore (D.Lgs. 138/2024 art. 3; registrazione dal 1 gennaio al 28 febbraio di ogni anno); l'obbligo di notifica a 24 e 72 ore della L. 90/2024 art. 1 vale per i Comuni sopra 100.000 abitanti e i capoluoghi (registro fonti 02); la Circolare AgID 2/2017 chiede un modulo di implementazione delle misure minime; il contatto del RTD non compare nel sito; i log degli amministratori di sistema vanno conservati almeno 6 mesi (provv. 27.11.2008, efficace «in quanto compatibile»).

## Norma
D.Lgs. 138/2024 (NIS2), L. 90/2024 art. 1, Circolare AgID 18.4.2017 n. 2/2017, CAD art. 17 (RTD), provv. Garante 27.11.2008. Testi gia' scaricati nel registro fonti 02 e 03.

## Requisiti
1. Questionario per l'ente (popolazione, soggetto NIS2 si/no, RTD nominato, misure minime compilate) con risposta scritta.
2. Procedura di notifica degli incidenti in `docs/`: chi, a chi, in quanto tempo, con i contatti reali (CSIRT Italia) forniti dall'ente.
3. Contatto del RTD nel footer e nella dichiarazione di accessibilita' quando fornito.
4. Registro degli accessi degli amministratori di sistema con conservazione di almeno 6 mesi: coordinare con DCP-141.

## Criteri di accettazione
- [ ] Questionario compilato dall'ente e allegato.
- [ ] Procedura di incidente pubblicata in `docs/` e controllata da chi risponde al telefono dell'ente.

## Dipendenze e priorita'
Dati dell'ente (needs-data); DCP-141 e DCP-142. P2.
