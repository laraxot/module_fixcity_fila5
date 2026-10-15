---
id: dcp-164
title: "DCP-164: contatti del responsabile della protezione dei dati raggiungibili dalla home"
type: story
epic: dc-parity-v2
status: needs-data
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "dpo rpd responsabile protezione dati contatti home footer provvedimento garante 186 2021 articolo 37 linee guida design 4.2.12 pagina"
related: [dcp-130, dcp-133, dcp-163]
---

# DCP-164: contatti del responsabile della protezione dei dati raggiungibili dalla home

## Lacuna e prova (2026-10-14)
Requisito 4.2.12 (Linee guida di design della PA, capitolo 4 «Requisiti» (versione corrente, letto il 2026-10-14)): i contatti del RPD devono stare in una sezione riconoscibile e raggiungibile dalla homepage. Misura: 4 pagine su 48 nominano il DPO (le pagine privacy, dove la sezione `dpo` e' un campo da compilare); nessun link dalla home o dal footer.

## Norma
GDPR art. 37 par. 7 (pubblicazione dei contatti del RPD e comunicazione al Garante); Linee guida di design 4.2.12 che rinviano al provvedimento del Garante 29.4.2021 n. 186 (non riletto in questa sessione: NON VERIFICATO nel registro fonti 02).

## Requisiti
1. Voce del footer «Responsabile della protezione dei dati» (4 lingue) verso la sezione `dpo` dell'informativa oppure una pagina dedicata, a scelta dell'ente.
2. I dati (nome o ufficio, email, PEC) li fornisce l'ente nel testo dell'informativa: il codice non li inventa.
3. Il blocco `contact` del footer non mostra il DPO se l'ente non l'ha designato (e lo dichiara).

## Criteri di accettazione
- [ ] `curl /it | grep` trova il link al DPO nel footer in it, en, de, es e il link risponde 200.
- [ ] Test: il link punta a una sezione che esiste e non e' `href="#"`.
- [ ] La scansione `lg-design-scan.py` passa da 44/48 a 0/48 «senza contatto DPO».

## Dipendenze e priorita'
Dati dell'ente (nome e contatti del DPO): needs-data. P1.
