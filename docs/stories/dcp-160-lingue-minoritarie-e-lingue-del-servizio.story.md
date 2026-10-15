---
id: dcp-160
title: "DCP-160: lingue minoritarie riconosciute e copertura delle quattro lingue"
type: story
epic: dc-parity-v2
status: needs-data
priority: P3
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "lingue minoritarie legge 482 1999 cad art 3 punto di accesso telematico it en de es traduzioni copertura"
related: [dcp-112]
---
# DCP-160: lingue minoritarie riconosciute e copertura delle quattro lingue

## Lacuna e prova (matrice del 2026-10-13, righe N-37 e I-12)
Il sito e' servito in italiano, inglese, tedesco e spagnolo. Il 2026-10-14 una scansione di 48 combinazioni pagina-lingua (12 pagine pubbliche per 4 lingue) non ha trovato `lang` errati, `<h1>` mancanti o chiavi di traduzione visibili: i difetti I-10 e I-17 della matrice di ieri non sono piu' riproducibili e sono coperti dal test `PublicPagesStructureTest` (DCP-153). Resta aperta la sola domanda dell'ente: il Comune e' fra quelli delimitati per una lingua minoritaria (L. 482/1999 art. 3)? La matrice non lo sa (N-37, I-12 NON VERIFICATO).

## Norma
L. 15.12.1999 n. 482 artt. 2, 3, 7, 9; CAD art. 3 c. 1; Linee guida AgID sul punto di accesso telematico.

## Requisiti
1. Domanda all'ente, con risposta scritta: lingua minoritaria riconosciuta sul territorio, si/no.
2. Se si': aggiungere la lingua come quinta lingua del servizio (file `lang/`, JSON delle pagine, test di parita' dei testi) e il testo dell'informativa, della cookie policy e della dichiarazione di accessibilita' nella lingua.

## Criteri di accettazione
- [ ] Risposta dell'ente registrata.
- [ ] Con «si»: il test di parita' dei testi passa in 5 lingue.

## Dipendenze e priorita'
Dato dell'ente (needs-data). P3.
