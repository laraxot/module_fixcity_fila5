---
id: dcp-280
title: "DCP-280: completamento del progetto, storia ombrello"
type: story
epic: completamento-go-live
status: in-progress
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-26
updated: 2026-10-26
qmd: "dcp 280 ombrello completamento progetto go-live lacune registro onde dcp-281 dcp-282 dcp-283 dcp-284 dcp-285 dcp-286 dcp-287 dcp-288 dcp-289 dcp-290 dcp-291 dcp-299"
related: [dcp-200, dcp-281, dcp-282, dcp-283, dcp-284, dcp-285, dcp-286, dcp-287, dcp-288, dcp-289, dcp-290, dcp-291, dcp-299]
tags: [completamento, go-live, ombrello, bmad, front-office]
---

# DCP-280: completamento del progetto, storia ombrello

## Fase BMAD

Planning. Raccoglie le storie figlie e i criteri con cui si decide che il progetto e' completo. Non contiene codice. Il lavoro sta nelle figlie; qui si tiene l'elenco, lo stato e l'uscita.

## Origine

Richiesta dell'utente del 2026-10-26: analizzare a fondo il progetto e l'intero front office (ogni pagina, ogni flusso, da ospite e da utente collegato), dire da soli cosa manca e farlo, con BMAD, second brain e blueprint Filament, e riordinare i documenti di moduli e temi. Un esempio dato dall'utente: nei commenti manca la risposta al singolo commento e un editor ricco alla maniera Filament (fatto dalla sessione f5 in CMT-022).

## Documenti del completamento

- [Registro delle lacune](../bmad/completion-gap-register.md): 491 righe, due analisi.
- [Architettura](../bmad/completion-architecture.md): contratti e proprieta'.
- [Brainstorming](../bmad/completion-brainstorming.md): cause, rischi, domande al titolare.
- [Piano](../bmad/completion-planning.md): onde, file di chi, percorso critico, calendario.
- [Blueprint Filament](../bmad/completion-front-office-blueprint.md): come si costruiscono le funzioni mancanti.
- [Audit del front office](../bmad/completion-front-office-audit.md) e i tredici rapporti per intero in `bmad/completion-audit/`.
- Vista per proprietario: `docs/bmad/completion-gap-analysis.md` in ogni modulo e tema.

## Storie figlie

| Storia | Oggetto | Stato |
|---|---|---|
| [DCP-281](dcp-281-rinforzo-back-office-e-pagine-legacy.story.md) | Pannello operatore e pagine legacy | In corso (aperti A-23 e la schermata Mobile) |
| [DCP-282](dcp-282-igiene-di-sicurezza-radice-web-bootstrap-e-gate.story.md) | Igiene della radice web, bootstrap, controllo di go-live | Fatta |
| DCP-283 | Esecuzione dinamica e fail-open in Cms e Notify | Fatta |
| [DCP-284](dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md) | Notifiche del ciclo di vita | Fatta, con DD-1..DD-6 aperti |
| DCP-285 | Policy utenti, reset password, revoca consensi | Fatta, schema da chiedere |
| [DCP-286](dcp-286-wizard-invio-idempotente-e-conferma-onesta.story.md) | Wizard idempotente, conferma onesta | In corso |
| [DCP-287](dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md) | Dettaglio del proprietario, follower, ritorno dopo il login | In corso |
| DCP-288 | Ricerca, intestazione accessibile, metadati | In corso |
| DCP-289 | Dati dell'ente, blocchi che si nascondono | In corso |
| DCP-290 | Pagine di accesso e reset password | In corso |
| DCP-291 | Accessibilita' di traccia, FAQ, banner, valutazione, `main` | In corso |
| DCP-299 | Riordino dei documenti di moduli e temi | Da scrivere |

Le storie dell'onda 4 si numerano quando serve, con il controllo che blocca.

## Criteri di uscita

La storia ombrello e' chiusa quando:

1. Tutte le lacune bloccanti del registro sono chiuse con prova, oppure marcate DA DECIDERE con il nome di chi decide.
2. Il controllo di go-live (`bashscripts/tools/release/go-live-gate.py`) non ha rossi dovuti al codice. I rossi dovuti a dati mancanti dell'ente o a testi legali restano rossi per costruzione, e sono elencati.
3. Una build del tema, una verifica visiva a tre larghezze e axe sulle pagine toccate mostrano che nessun difetto bloccante di accessibilita' e' rimasto.
4. La suite dei moduli toccati e' verde o i rossi sono spiegati con la prova che esistevano prima.
5. PHPStan sui moduli e' a zero e il suo perimetro e' dichiarato.
6. Il second brain e' aggiornato: memorie, registro delle decisioni di ogni modulo e tema toccato, indice dei documenti.

## Vincoli

Nessun commit e nessun cambio di ramo da parte degli agenti. Nessuna migrazione e nessuna dipendenza nuova senza approvazione. Nessuna rimozione con `rm`. Un solo autore per file. Nessuna ricostruzione del tema se non quella coordinata. I valori dei segreti non compaiono nei documenti.

## Esito

Da scrivere a chiusura, con i comandi e gli output reali.
