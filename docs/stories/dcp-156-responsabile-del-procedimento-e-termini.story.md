---
id: dcp-156
title: "DCP-156: responsabile del procedimento, termine di risposta e segnalazione come istanza (L. 241/1990)"
type: story
epic: dc-parity-v2
status: needs-decision
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "legge 241 1990 responsabile procedimento termine risposta istanza segnalazione ticket unita organizzativa art 2 4 5 8"
related: [dcp-112, dcp-133, dcp-144]
---
# DCP-156: responsabile del procedimento, termine di risposta e segnalazione come istanza (L. 241/1990)

## Lacuna e prova (matrice del 2026-10-13, riga N-34 NON VERIFICATO)
La matrice normativa lascia la riga N-34 senza story: la segnalazione di un cittadino puo' costituire un'istanza che avvia un procedimento amministrativo, e per questo servono responsabile, termine e comunicazione di avvio. Il ticket ha stati (`pending`, `in_review`, `in_progress`, `resolved`, `closed`) e una timeline con attore e motivo, ma **nessun campo per il responsabile del procedimento e nessun termine** (`Modules/Fixcity/app/Enums/TicketStatusEnum.php`). L'SLA di cui parla il PRD (FR-010) e' un carico di lavoro interno, non un termine di legge.

## Norma
L. 7.8.1990 n. 241 artt. 2 (dovere di provvedimento, termine di 30 giorni se non diversamente previsto), 4-5 (unita' organizzativa e responsabile), 8 (comunicazione di avvio). Testo da leggere su Normattiva prima di fissare i valori: la matrice lo segna NON VERIFICATO.

## Requisiti
1. Decisione dell'ente: la segnalazione e' un'istanza ai sensi della L. 241/1990 oppure una collaborazione senza obbligo di provvedimento? Se e' un'istanza, quali tipi e con quale termine.
2. Se si': campo «responsabile del procedimento» e «unita' organizzativa» sul ticket (dati forniti dall'ente, mai inventati), termine e data di scadenza visibili al cittadino nella pagina di tracciamento, comunicazione di avvio all'invio.
3. Se no: una frase chiara nella pagina di invio e nell'informativa («la segnalazione non e' un'istanza»), scritta dall'ente.

## Criteri di accettazione
- [ ] Decisione dell'ente registrata nella story.
- [ ] Con «istanza»: il ticket mostra responsabile e termine, e il test lo prova; con «non istanza»: la frase compare in 4 lingue.

## Dipendenze e priorita'
DCP-133 (dati dell'ente). P1, needs-decision: la scelta e' dell'ente, non del codice.
